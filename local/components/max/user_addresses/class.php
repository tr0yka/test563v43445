<?php

use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;

class MaxUserAddresses extends CBitrixComponent
{
    /**
     * Название HL блока
     */
    const USER_ADDRESSES_HLB_NAME = 'UserAddresses';

    /**
     * Количество элементов на траницу
     */
    const PER_PAGE = 10;

    /**
     * Получение адресов пользователя
     * @param $userId integer
     * @param $activeOnly boolean
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getAddresses($userId, $offset, $limit, bool $activeOnly = false)
    {
        Loader::includeModule('highloadblock');
        $result = [
            'items' => [],
            'total' => 0,
        ];
        /**
         * ИД кэша. Кэшируются все страницы
         */
        $cacheId = 'user_addresses_' . serialize([
            $userId,
            $activeOnly,
            $limit,
            $offset
        ]);

        /**
         * Инициализация кэша
         */
        $cache = Cache::createInstance();
        if ($cache->initCache(7200, $cacheId, '/user_addresses/user_' . $userId)) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            /**
             * Компиляция и получение класса HL блока
             */
            $entity_data_class = $this->getDataClass();
            if ($entity_data_class) {
                $filter = [];
                if ($activeOnly) {
                    $filter['UF_IS_ACTIVE'] = $activeOnly;
                }
                $dbResult = $entity_data_class::getList([
                    "select"      => ["*"],
                    "order"       => ["UF_IS_ACTIVE" => "DESC",'ID' => 'ASC'],
                    "filter"      => $filter,
                    "count_total" => true,
                    "offset"      => $offset,
                    "limit"       => $limit,
                ]);
                while ($item = $dbResult->fetch()) {
                    $result['items'][$item['ID']] = $item;
                }
                $result['total'] = $dbResult->getCount();
            } else {
                /**
                 * Отмена кэширования, если HL блок не найден
                 */
                $cache->abortDataCache();
            }
            $cache->endDataCache($result);
        }
        //Конец кэширования данных
        return $result;
    }

    /**
     * Поиск и получение дата класса HL блока
     * @return null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getDataClass()
    {
        $result = null;
        $hlblock = HighloadBlockTable::getList(['filter' => ['NAME' => self::USER_ADDRESSES_HLB_NAME]])->fetch();
        if ($hlblock) {
            $this->arResult['HL_BLOCK_ID'] = $hlblock['ID'];
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $result = $entity->getDataClass();
        }
        return $result;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getData()
    {
        global $USER;
        /**
         * Инициализация навигации
         */
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-user-addresses");
        $nav->allowAllRecords(true)
            ->setPageSize(self::PER_PAGE)
            ->initFromUri();
        $items = [];
        if ($USER->IsAuthorized()) {
            $activeOnly = $this->arParams['SHOW_ONLY_ACTIVE'] === 'Y';
            /**
             * Получение адресов пользователея
             */
            $res = $this->getAddresses($USER->GetID(), $nav->getOffset(), $nav->getLimit(), $activeOnly);
            $items = $res['items'];
            /**
             * максимальное количество записей по фильтру
             */
            $nav->setRecordCount($res['total']);
        }
        return [
            'ITEMS' => $items,
            'NAV'   => $nav
        ];
    }

    /**
     * @return mixed|void|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function executeComponent()
    {
        $this->arResult['RESULT'] = $this->getData();
        $this->includeComponentTemplate();
    }
}