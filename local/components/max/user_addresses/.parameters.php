<?php
CModule::IncludeModule("iblock");


$arComponentParameters = [
    "GROUPS"     => [
        "BASE" => [
            "NAME" => GetMessage("BASE_SETTINGS")
        ]
    ],
    "PARAMETERS" => [
        "SHOW_ONLY_ACTIVE" => [
            "PARENT"   => "BASE",
            "NAME"     => GetMessage("SHOW_ONLY_ACTIVE"),
            "TYPE"     => "CHECKBOX",
            "MULTIPLE" => "N",
            "DEFAULT"  => "Y",
        ]
    ]
];