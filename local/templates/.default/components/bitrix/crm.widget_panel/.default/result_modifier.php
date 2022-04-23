<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
Loader::includeModule('crm');

$newarResult = $arResult; 

foreach($newarResult['ROWS'] as $key => $item) {
    foreach($item['cells'] as $seckey => $secitem) {
        if($secitem['controls'][0]['typeName']=='funnel' && $secitem['controls'][0]['entityTypeName']=='LEAD') {
            \Bitrix\Main\Diag\Debug::writeToFile($key, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
            //$secitem['controls'][0]['title'] = 'воронок'; 
            $currentdata = $arResult['ROWS'][$key]['cells'][$seckey]['data'];
            foreach($currentdata['0']['items'] as &$thirditem) {
                $opportunitydata = getLeadssum(["STATUS_ID"=>$thirditem['ID']]);
                \Bitrix\Main\Diag\Debug::writeToFile($opportunitydata, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");

                $thirditem['TOTAL'] = $thirditem['TOTAL'].' на сумму: '.$opportunitydata;
            } 
            \Bitrix\Main\Diag\Debug::writeToFile($currentdata, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");

            //$arResult['ROWS'][$key]['cells'][$seckey]['data'] = []; 
            unset($thirditem);
            //\Bitrix\Main\Diag\Debug::writeToFile($arResult['ROWS'][$key]['cells'][$seckey]['data'], "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
        }
    }
    //unset($secitem);
}

function getLeadssum($filter = []) {
    $arFilter = [
        //"=COMPANY_ID" => 0,
        //"STATUS_ID" => $stage,
        "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
    ];
    $arFilter = array_merge($arFilter, $filter);
    $arSelect = [
        "ID",
        "ACCOUNT_CURRENCY_ID",
        "OPPORTUNITY_ACCOUNT"
    ];
    $res = \CCrmLead::GetListEx(Array(), $arFilter, false, false, $arSelect);
    $currencies = [];
    while($lead = $res->Fetch()) {
        $currencies[$lead['ACCOUNT_CURRENCY_ID']] += $lead['OPPORTUNITY_ACCOUNT'];    
    }
    \Bitrix\Main\Diag\Debug::writeToFile($currencies, "resfunnel".date("d.m.Y G.i.s"), "__debug.log");
    
    $current = http_build_query($currencies, '', ' ');
    return $current;

}

