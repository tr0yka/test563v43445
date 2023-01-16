<?php

/**
 * Функция удажения кэша пользователя
 */
if(!function_exists('clearAddressesCache')){
    function clearAddressesCache($uid)
    {
        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cache->cleanDir('/user_addresses/user_' . $uid);
    }
}

$eventManager = \Bitrix\Main\EventManager::getInstance();

/**
 * Событие после обновления адреса
 */
$eventManager->addEventHandler('', 'UserAddressesOnBeforeUpdate', 'UserAddressesOnBeforeUpdate');
function UserAddressesOnBeforeUpdate(\Bitrix\Main\Entity\Event $event)
{
    $arFields = $event->getParameter("fields");
    clearAddressesCache($arFields['UF_USER_ID']);
}


/**
 * Событие после удления адреса
 */
$eventManager->addEventHandler('', 'UserAddressesOnDelete', 'UserAddressesOnDelete');
function UserAddressesOnDelete(\Bitrix\Main\Entity\Event $event)
{
    $arFields = $event->getParameter("fields");
    clearAddressesCache($arFields['UF_USER_ID']);
}