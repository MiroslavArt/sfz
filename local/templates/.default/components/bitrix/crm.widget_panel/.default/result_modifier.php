<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
Loader::includeModule('crm');

$newarResult = $arResult; 

$filter = []; 

if($arResult['FILTER_FIELDS']['RESPONSIBLE_ID']) {
    $filter['ASSIGNED_BY_ID'] = $arResult['FILTER_FIELDS']['RESPONSIBLE_ID'];
}

if($arResult['FILTER_FIELDS']['PERIOD_from']) {
    $filter['>=DATE_CREATE'] = $arResult['FILTER_FIELDS']['PERIOD_from'];
}

if($arResult['FILTER_FIELDS']['PERIOD_to']) {
    $filter['<=DATE_CREATE'] = $arResult['FILTER_FIELDS']['PERIOD_to'];
}

foreach($newarResult['ROWS'] as $key => $item) {
    foreach($item['cells'] as $seckey => $secitem) {
        if($secitem['controls'][0]['typeName']=='funnel' && $secitem['controls'][0]['entityTypeName']=='LEAD') {
             
            $currentdata = $arResult['ROWS'][$key]['cells'][$seckey]['data'];
            foreach($currentdata['0']['items'] as &$thirditem) {
                $opportunitydata = getLeadssum(array_merge($filter, ["STATUS_ID"=>$thirditem['ID']]));
               

                $thirditem['NAME'] = $thirditem['NAME'].' '.$opportunitydata;
            } 
            

            $arResult['ROWS'][$key]['cells'][$seckey]['data'] = $currentdata; 

            unset($thirditem);
            
        }
    }
    
}

function getLeadssum($filter = []) {
    $arFilter = [
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
    
    
    $current = http_build_query($currencies, '', ' ');
    return $current;

}

