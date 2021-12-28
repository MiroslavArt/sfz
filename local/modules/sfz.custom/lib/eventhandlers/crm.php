<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;

class Crm
{
    public static function onAfterCrmCompanyUpdate(&$arFields)
    {
        Loader::includeModule('crm');

        
        //\Bitrix\Main\Diag\Debug::writeToFile($arFields, "export1", "__miros.log");
        if(count($arFields)>4) {
            $arFilter = [
                "ID" => $arFields["ID"], //выбираем определенную сделку по ID
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "*",
                "UF_*"
            ];
            $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
            $arCompany = $res->fetch();
            if($arCompany[idGalUF]) {
                $root = simplexml_load_string('<Catalog><Contragent></Contragent></Catalog>');
                $root->Contragent->addAttribute('id', $arCompany[idGalUF]);
                $root->Contragent->Market = '1';
                $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("m.d.y").':'.date("H.i.s").'companyupdate.xml');
            }
        }
    }
    
    public static function onCrmRequisiteUpdate(&$arFields)
    {
        Loader::includeModule('crm');
        \Bitrix\Main\Diag\Debug::writeToFile($arFields, "export1", "__miros.log");
        /*if(count($arFields)>4) {
            $arFilter = [
                "ID" => $arFields["ID"], //выбираем определенную сделку по ID
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "*",
                "UF_*"
            ];
            $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
            $arCompany = $res->fetch();
            if($arCompany[idGalUF]) {
                $root = simplexml_load_string('<Catalog><Contragent></Contragent></Catalog>');
                $root->Contragent->addAttribute('id', $arCompany[idGalUF]);
                $root->Contragent->Market = '1';
                $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("m.d.y").':'.date("H.i.s").'companyupdate.xml');
            }
        }*/
    }
    
    public static function onBeforeCrmDealUpdate(&$arFields)
    {
        /*if (\Bitrix\Main\Loader::includeModule('crm')) {
            if($arFields['COMPANY_ID']) {
                $requisite = new \Bitrix\Crm\EntityRequisite();
                $rs = $requisite->getList([
                    "filter" => ["ENTITY_ID" => $arFields['COMPANY_ID'], "ENTITY_TYPE_ID" => \CCrmOwnerType::Company,
                    ]
                ]);
                $reqData = $rs->fetch();
                if(!$reqData || !$reqData['RQ_INN']) {
                    \Bitrix\Main\Loader::includeModule('im');
                    $arFieldschat = array(
                        "MESSAGE_TYPE" => "S", # P - private chat, G - group chat, S - notification
                        "TO_USER_ID" => $arFields['CREATED_BY_ID'],
                        "FROM_USER_ID" => 1,
                        "MESSAGE" => "Невозможно привязать компанию к сделке, так как в ней не заполнено поле ИНН. ",
                        "AUTHOR_ID" => 1

                    );
                    \CIMMessenger::Add($arFieldschat);
                    $arFields['COMPANY_ID'] = "";

                }
            }
        }


        if (\Bitrix\Main\Loader::includeModule('crm')) {
            if($arFields[DEPARTURE_UF] && !$arFields[ARRIVAL_UF]) {
                $foundf = ARRIVAL_UF;
            } elseif(!$arFields[DEPARTURE_UF] && $arFields[ARRIVAL_UF]) {
                $foundf = DEPARTURE_UF;
            }
            $arFilterDeal = array('ID'=>$arFields['ID']);
            $arSelectDeal = array('ID', $foundf);
            $obResDeal = \CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectDeal)->Fetch();
            if($obResDeal[$foundf]) {
                $arFields[$foundf] = $obResDeal[$foundf];
            }
        }

        if($arFields[DEPARTURE_UF] && $arFields[ARRIVAL_UF]) {
            $routeid = self::actualiseRoute($arFields[DEPARTURE_UF], $arFields[ARRIVAL_UF]);
            if(intval($routeid) > 0) {
                $arFields[ROUTE_UF] = $routeid;
            }
        }*/

    }

    public static function onBeforeCrmDealAdd(&$arFields)
    {
        /*if (\Bitrix\Main\Loader::includeModule('crm')) {
            if($arFields['COMPANY_ID']) {
                $requisite = new \Bitrix\Crm\EntityRequisite();
                $rs = $requisite->getList([
                    "filter" => ["ENTITY_ID" => $arFields['COMPANY_ID'], "ENTITY_TYPE_ID" => \CCrmOwnerType::Company,
                    ]
                ]);
                $reqData = $rs->fetch();
                if(!$reqData || !$reqData['RQ_INN']) {
                    \Bitrix\Main\Loader::includeModule('im');
                    $arFieldschat = array(
                        "MESSAGE_TYPE" => "S", # P - private chat, G - group chat, S - notification
                        "TO_USER_ID" => $arFields['CREATED_BY_ID'],
                        "FROM_USER_ID" => 1,
                        "MESSAGE" => "Невозможно привязать компанию к сделке, так как в ней не заполнено поле ИНН. ",
                        "AUTHOR_ID" => 1

                    );
                    \CIMMessenger::Add($arFieldschat);
                    $arFields['COMPANY_ID'] = "";

                }
            }
        }


        if($arFields[DEPARTURE_UF] && $arFields[ARRIVAL_UF]) {
            $routeid = self::actualiseRoute($arFields[DEPARTURE_UF], $arFields[ARRIVAL_UF]);
            if(intval($routeid) > 0) {
                $arFields[ROUTE_UF] = $routeid;
            }
        }*/
    }

    /*protected static function actualiseRoute($departure = null, $arrival = null)
    {
        $logib = Utils::getIDIblockByCode(IBPL_LOGSECT, IBPL_TYPE);
        $routeib = Utils::getIDIblockByCode(IBPL_ROUTE, IBPL_TYPE);
        if(!is_null($departure) && !is_null($arrival)) {
            $ibdept = Utils::getIblockElementByID($logib, $departure);
            $logdept = $ibdept['PROPERTIES']['LOGSEKTOR']['VALUE'];
            $ibarr =  Utils::getIblockElementByID($logib, $arrival);
            $logarr = $ibarr['PROPERTIES']['LOGSEKTOR']['VALUE'];
            $routes = Utils::getIBlockElementsByConditions($routeib, ['PROPERTY_LOG_OTPRAVLENIYA'=>$logdept, 'PROPERTY_LOG_PRIBYTIYA'=>$logarr]);
            if(empty($routes)) {
                $ID = Utils::createIBlockElement($routeib, ['NAME'=>$logdept.'->'.$logarr, 'ACTIVE' => 'Y',
                    'PROPERTY_VALUES' => [
                        'LOG_OTPRAVLENIYA'=>$logdept,
                        'LOG_PRIBYTIYA' => $logarr
                    ]
                ], []);
                return $ID;
            } else {
                return $routes[0]['ID'];
            }
        }
    }*/
}
