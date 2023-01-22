<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Config\Option;
use Bitrix\Main\Data\Cache;

class SimpleNews extends CBitrixComponent
{

    public function __construct($component = null)
    {
        parent::__construct($component);
        \Bitrix\Main\Loader::includeModule('iblock');
        $this->arParams['CACHE_TIME'] = ($this->arParams["CACHE_TIME"]) ? $this->arParams["CACHE_TIME"] : 3600;

    }

    protected function getItems($offset,$limit)
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $filter = ['ACTIVE' => 'Y',];
        if($request->get('year')){
            $year = $request->get('year');
        }else{
            $year = date('Y');
        }

        $filter['>ACTIVE_FROM'] = date('d.m.Y',mktime(0,0,0,1,1,$year));
        $filter['<ACTIVE_FROM'] = date('d.m.Y',mktime(0,0,0,1,1,$year+1));

        $items = [];
        $total = 0;
        if($this->arParams["CACHE_TYPE"] == "N" || $this->arParams["CACHE_TYPE"] == "A" && Option::get("main", "component_cache_on", "Y") == "N"){
            $CACHE_TIME = 0;
        }else{
            $CACHE_TIME = $this->arParams["CACHE_TIME"];
        }

        $cacheId = 'simple_news_'.$offset.'_'.$limit.'_'.serialize($filter);
        $cache = Cache::createInstance();
        if ($cache->initCache($CACHE_TIME, $cacheId,'simple_news')) {
            $res = $cache->getVars();
            $items = $res['ITEMS'];
            $total = $res['TOTAL'];
        } elseif ($cache->startDataCache()) {
            $ibDataClass = \Bitrix\Iblock\Iblock::wakeUp($this->arParams['IBLOCK_ID'])->getEntityDataClass();
            $dbRes = $ibDataClass::getList([
                'select' => ['ID','NAME', 'ACTIVE_FROM','PREVIEW_TEXT', 'PREVIEW_PICTURE'],
                'filter' => $filter,
                'order' => ['ACTIVE_FROM' => 'DESC', 'ID' => 'DESC'],
                "count_total" => true,
                "offset" => $offset,
                "limit" => $limit,
            ]);
            $filesIds = [];
            $files = [];
            while ($item = $dbRes->fetch()){
                $filesIds[] = $item['PREVIEW_PICTURE'];
                $items[$item['ID']] = $item;
            }

            $filesRes = \Bitrix\Main\FileTable::getList([
                'filter' => ['ID' => $filesIds]
            ]);

            while ($file = $filesRes->fetch()){
                $files[$file['ID']] = $file;
            }

            foreach ($items as &$item){
                if($item['PREVIEW_PICTURE']){
                    $item['PREVIEW_PICTURE'] = $files[$item['PREVIEW_PICTURE']];
                    $item['PREVIEW_PICTURE']['SRC'] = CFIle::GetFileSRC($item['PREVIEW_PICTURE']);
                }
            }
            unset($item);


            $total = $dbRes->getCount();

            $cache->endDataCache(['ITEMS' => $items, 'TOTAL' => $total]);
        }

        return [
            'ITEMS' => $items,
            'TOTAL' => $total,
        ];
    }

    protected function getYears()
    {
        $res = [];

        $cache = Cache::createInstance();
        if ($cache->initCache($this->arParams['CACHE_TIME'], "simple_news_years")) {
            $res = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $ibDataClass = \Bitrix\Iblock\Iblock::wakeUp($this->arParams['IBLOCK_ID'])->getEntityDataClass();
            $dbRes = $ibDataClass::getList([
                'select' => ['ID','ACTIVE_FROM'],
                'filter' => ['ACTIVE' => 'Y', '<=ACTIVE_FROM' => date('d.m.Y')],
            ]);
            while ($item = $dbRes->fetch()){
                $res[] = (new \DateTime($item['ACTIVE_FROM']))->format('Y');
            }
            $res = array_unique($res);
            arsort($res);
            $cache->endDataCache($res);
        }

        return $res;
    }

    protected function getData()
    {
        global $APPLICATION;
        $nav = new \Bitrix\Main\UI\PageNavigation("nav-news");
        $nav->allowAllRecords(true)
            ->setPageSize(($this->arParams['PER_PAGE']) ? $this->arParams['PER_PAGE'] : 5)
            ->initFromUri();
        $res = $this->getItems($nav->getOffset(), $nav->getLimit());
        $nav->setRecordCount($res['TOTAL']);
        $APPLICATION->SetPageProperty('title',GetMessage('PAGE_TITLE',['#COUNT#' => $res['TOTAL']]));
        return [
            'ITEMS' => $res['ITEMS'],
            'NAV'   => $nav
        ];
    }

    public function executeComponent()
    {
        $this->arResult['ITEMS'] = [];

        if($this->arParams['IBLOCK_ID']){
            $this->arResult['YEARS'] = $this->getYears();
            $this->arResult['DATA'] = $this->getData();
        }

        $this->includeComponentTemplate();
    }


}