<?php

use \Bitrix\Main\Localization\Loc;

if(!empty($arResult['RESULT']['ITEMS'])){
    global $APPLICATION;
    $items = [];
    foreach ($arResult['RESULT']['ITEMS'] as $item){
        $items[] = [
            'data' => [
                'ID' => $item['ID'],
                'ADDRESS' => $item['UF_ADDRESS'],
                'IS_ACTIVE' => ($item['UF_IS_ACTIVE'] == 1) ? Loc::getMessage("YES") : Loc::getMessage("NO")
            ],
            'actions' => [
                [
                    'text'    => Loc::getMessage("EDIT"),
                    'onclick' => 'document.location.href="/bitrix/admin/highloadblock_row_edit.php?ENTITY_ID='.$arResult['HL_BLOCK_ID'].'&ID='.$item['ID'].'"'
                ],
            ],
        ];
    }
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID'                   => 'report_list',
            'COLUMNS'                   => [
                [
                    'id'      => 'ID',
                    'name'    => 'ID',
                    'sort'    => 'ID',
                    'default' => true
                ],
                [
                    'id'      => 'ADDRESS',
                    'name'    => Loc::getMessage('ADDRESS'),
                    'sort'    => 'ADDRESS',
                    'default' => true
                ],
                [
                    'id'      => 'IS_ACTIVE',
                    'name'    => Loc::getMessage('ACTIVE'),
                    'sort'    => 'IS_ACTIVE',
                    'default' => true
                ],
            ],
            'ROWS'                      => $items,
            'NAV_OBJECT'                => $arResult['RESULT']['NAV'],
            'AJAX_MODE'                 => 'Y',
            'AJAX_ID'                   => \CAjax::getComponentID('max:user_addresses', '.default', ''),
            'PAGE_SIZES'                => [
                [
                    'NAME'  => "5",
                    'VALUE' => '5'
                ],
                [
                    'NAME'  => '10',
                    'VALUE' => '10'
                ],
                [
                    'NAME'  => '20',
                    'VALUE' => '20'
                ],
                [
                    'NAME'  => '50',
                    'VALUE' => '50'
                ],
                [
                    'NAME'  => '100',
                    'VALUE' => '100'
                ]
            ],
            'AJAX_OPTION_JUMP'          => 'N',
            'SHOW_ROW_CHECKBOXES'       => false,
            'SHOW_CHECK_ALL_CHECKBOXES' => false,
            'SHOW_ROW_ACTIONS_MENU'     => true,
            'SHOW_GRID_SETTINGS_MENU'   => false,
            'SHOW_NAVIGATION_PANEL'     => true,
            'SHOW_PAGINATION'           => true,
            'SHOW_SELECTED_COUNTER'     => false,
            'SHOW_TOTAL_COUNTER'        => false,
            'SHOW_PAGESIZE'             => true,
            'SHOW_ACTION_PANEL'         => false,
            'ACTION_PANEL'              => [
                'GROUPS' => [
                    'TYPE' => [
                        'ITEMS' => [],
                    ]
                ],
            ],
            'ALLOW_COLUMNS_SORT'        => false,
            'ALLOW_COLUMNS_RESIZE'      => false,
            'ALLOW_HORIZONTAL_SCROLL'   => false,
            'ALLOW_SORT'                => false,
            'ALLOW_PIN_HEADER'          => false,
            'AJAX_OPTION_HISTORY'       => 'N'
        ]
    );
}
?>