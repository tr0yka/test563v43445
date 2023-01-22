<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;
?>

<div class="wrapper">
    <div class="title"><?=$APPLICATION->GetPageProperty('title')?></div>
    <? if(!empty($arResult['DATA']['ITEMS'])): ?>

        <? if(!empty($arResult['YEARS'])): ?>
            <div class="tabs">
                <? foreach ($arResult['YEARS'] as $year): ?>
                    <div>
                        <a href="<?=$APPLICATION->GetCurDir()?>?year=<?=$year?>"><?=$year?></a>
                    </div>
                <? endforeach; ?>
            </div>
        <? endif ?>

        <div class="news">
            <? foreach ($arResult['DATA']['ITEMS'] as $item): ?>
                <div class="news-item">
                    <div><?=$item['NAME']?></div>
                    <div><?=$item['ACTIVE_FROM']?></div>
                    <div>
                        <? if($item['PREVIEW_PICTURE']): ?>
                            <?
                            $image = CFile::ResizeImageGet($item['PREVIEW_PICTURE'],['width' => 120,'height' => 120]);
                            ?>
                            <img src="<?=$image['src']?>" alt="">
                        <? endif; ?>
                    </div>
                    <div><?=$item['PREVIEW_TEXT']?></div>
                </div>
            <? endforeach; ?>
        </div>
    <? endif ?>
</div>

<?
$APPLICATION->IncludeComponent(
    "bitrix:main.pagenavigation",
    "",
    [
        "NAV_OBJECT" => $arResult['DATA']['NAV'],
        "SEF_MODE"   => "N",
        "SHOW_COUNT" => "N",
    ],
    false
);
