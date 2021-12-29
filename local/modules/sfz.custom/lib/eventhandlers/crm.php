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

        \Bitrix\Main\Diag\Debug::writeToFile($arFields, "export1", "__miros.log");
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
                $export = false;
                if($arCompany['TITLE']) {
                    $export = true;
                    $root->Contragent->org = $arCompany['TITLE'];
                }
                if($arFields['FM']['EMAIL']) {
                    foreach($arFields['FM']['EMAIL'] as $item) {
                        if($item['VALUE']) {
                            if($email) {
                                $email = $email.','.$item['VALUE'];  
                            } else {
                                $email = $item['VALUE']; 
                            }
                        }
                    }
                    if($email) {
                        $export = true;
                        $root->Contragent->email = $email; 
                    }
                }
                
                if($arFields['FM']['PHONE']) {
                    foreach($arFields['FM']['PHONE'] as $item) {                     
                        if($item['VALUE']) {
                            if($phone) {
                                $phone = $phone.','.$item['VALUE'];  
                            } else {
                                $phone = $item['VALUE']; 
                            }
                        }
                    }                  
                    if($phone) {
                        $export = true;
                        $root->Contragent->tel = $phone; 
                    }
                }
                if($arCompany[marketnameUF]) {
                    $export = true;
                    $root->Contragent->name1 = $arCompany[marketnameUF];
                }
                if($arCompany[marketthroughnameUF]) {
                    $export = true;
                    $root->Contragent->name2 = $arCompany[marketthroughnameUF];
                }
                if($arCompany[marketthroughnameUF]) {
                    $export = true;
                    $root->Contragent->name2 = $arCompany[marketthroughnameUF];
                }
                if($arCompany[dealerSyPlyUF]) {
                    $export = true;
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arCompany[dealerSyPlyUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $root->Contragent->dealerply = $ibid['NAME'];
                    }
                }
                if($arCompany[dealerLamUF]) {
                    $export = true;
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arCompany[dealerLamUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $root->Contragent->dealerply = $ibid['NAME'];
                    }
                }
                if($arCompany[marketiID]) {
                    $export = true;
                    $fieldval = Utils::getEnumvalue(marketiID, $arCompany[marketiID], 'value');
                    $root->Contragent->market = $fieldval;
                }
                if($arCompany[statusdealUF]) {
                    $export = true;
                    $root->Contragent->daelerlamarty2 = $arCompany[statusdealUF];
                }
                if($arCompany[partncodeUF]) {
                    $export = true;
                    $root->Contragent->partner = $arCompany[partncodeUF];
                }
                if($arCompany[furnitcompUF]) {
                    $export = true;
                    $root->Contragent->mebel = $arCompany[furnitcompUF];
                }
                if($arCompany[eng1UF]) {
                    $export = true;
                    $root->Contragent->name1eng = $arCompany[eng1UF];
                }
                if($arCompany[eng2UF]) {
                    $export = true;
                    $root->Contragent->name2eng = $arCompany[eng2UF];
                }
                if($arCompany[manSyPlyUF]) {
                    $export = true;
                    $user = Utils::getUserbycondition(array('=ID' =>$arCompany[manSyPlyUF]));
                    $root->Contragent->managerplyemail = $user['EMAIL'];
                }
                if($arCompany[manLamUF]) {
                    $export = true;
                    $user = Utils::getUserbycondition(array('=ID' =>$arCompany[manLamUF]));
                    $root->Contragent->managerplyemail = $user['EMAIL'];
                }
                if($export) {
                    $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("m.d.y").'_'.date("H.i.s").'_'.'companyupdate.xml');
                }
            }
        }
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
