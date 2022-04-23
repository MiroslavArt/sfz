<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$newarResult = $arResult; 

foreach($newarResult['ROWS'] as $key => $item) {
    foreach($item['cells'] as $seckey => $secitem) {
        if($secitem['controls'][0]['typeName']=='funnel' && $secitem['controls'][0]['entityTypeName']=='LEAD') {
            \Bitrix\Main\Diag\Debug::writeToFile($key, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
            //$secitem['controls'][0]['title'] = 'воронок'; 
            $currentdata = $arResult['ROWS'][$key]['cells'][$seckey]['data'];
            foreach($currentdata['0']['items'] as &$thirditem) {
                

            } 
            \Bitrix\Main\Diag\Debug::writeToFile($currentdata, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");

            //$arResult['ROWS'][$key]['cells'][$seckey]['data'] = []; 
            unset($thirditem);
            //\Bitrix\Main\Diag\Debug::writeToFile($arResult['ROWS'][$key]['cells'][$seckey]['data'], "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
        }
    }
    //unset($secitem);
}

