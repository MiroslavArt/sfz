<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$newarResult = $arResult; 

foreach($newarResult['ROWS'] as $key => $item) {
    foreach($item['cells'] as $seckey => $secitem) {
        if($secitem['controls'][0]['typeName']=='funnel' && $secitem['controls'][0]['entityTypeName']=='LEAD') {
            \Bitrix\Main\Diag\Debug::writeToFile('inside', "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
            //$secitem['controls'][0]['title'] = 'воронок'; 
            $arResult[$key]['cells'][$seckey]['controls'][0]['data'] = []; 
        }
    }
    //unset($secitem);
}

\Bitrix\Main\Diag\Debug::writeToFile($arResult, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");