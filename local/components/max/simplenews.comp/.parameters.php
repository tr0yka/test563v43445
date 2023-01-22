<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("iblock");



$arComponentParameters = [
    "GROUPS"     => [
        "BASE" => [
            "NAME" => GetMessage("BASE_SETTINGS")
        ]
    ],
    "PARAMETERS" => [
        "IBLOCK_ID" => [
            "PARENT"   => "BASE",
            "NAME"     => GetMessage("IBLOCK_ID"),
            "TYPE"     => "STRING",
            "MULTIPLE" => "N",
        ],
        "PER_PAGE" => [
            "PARENT"   => "BASE",
            "NAME"     => GetMessage("PER_PAGE"),
            "TYPE"     => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT"  => "5",
        ],
        "CACHE_TYPE" => array(
            "NAME" => GetMessage("CACHE_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => array(
                "A" => GetMessage("COMP_PROP_CACHE_TYPE_AUTO"),
                "Y" => GetMessage("COMP_PROP_CACHE_TYPE_YES"),
                "N" => GetMessage("COMP_PROP_CACHE_TYPE_NO"),
            ),
            "DEFAULT" => "N",
            "ADDITIONAL_VALUES" => "N",
        ),
        "CACHE_TIME" => array(
            "NAME" => GetMessage("COMP_PROP_CACHE_TIME"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => 3600,
            "COLS" => 5,
        ),
    ]
];