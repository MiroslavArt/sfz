<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Main\Localization\Loc;

class Crm
{
    public static function onAfterCrmCompanyUpdate(&$arFields)
    {
        Loader::includeModule('crm');
        Loader::includeModule('sfz.custom');
        //\Bitrix\Main\Diag\Debug::writeToFile($arFields, "export1", "__miros.log");
        if(makeexportXML=='Y' && rootXML) {
            if(count($arFields)>4 && !array_key_exists(hashUF, $arFields)) {
                $companyid = $arFields["ID"];
                $PROPS = [];
                //$PROP = []; 
                if($arFields['TITLE']) {
                    $PROPS['org'] = $arFields['TITLE'];
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
                        $PROPS['email'] = $email;
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
                        $PROPS['tel'] = $email;
                    }
                }
                if($arFields[marketnameUF]) {
                    $PROPS['name1'] = $arFields[marketnameUF];
                }
                if($arFields[marketthroughnameUF]) {
                    $PROPS['name2'] = $arFields[marketthroughnameUF];
                }
                
                if($arFields[dealerSyPlyUF]) {
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arFields[dealerSyPlyUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $PROPS['dealerply'] = $ibid['NAME'];
                    }
                }
                if($arFields[dealerLamUF]) {
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arFields[dealerLamUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $PROPS['daelerlamarty'] = $ibid['NAME'];
                    }
                }
                if($arFields[marketUF]) {
                    $fieldval = Utils::getEnumvalue(marketiID, $arFields[marketUF], 'value');
                    if($fieldval) {
                        $PROPS['market'] = $fieldval;
                    }
                }
                if($arFields[statusdealUF]) {
                    $PROPS['daelerlamarty2'] = $arFields[statusdealUF];
                }
                if($arFields[partncodeUF]) {
                    $PROPS['partner'] = $arFields[partncodeUF];
                }
                if($arFields[furnitcompUF]) {
                    $PROPS['mebel'] = $arFields[furnitcompUF];
                }
                if($arFields[eng1UF]) {
                    $PROPS['name1eng'] = $arFields[eng1UF];
                }
                if($arFields[eng2UF]) {
                    $PROPS['name2eng'] = $arFields[eng2UF];
                }
                if($arFields[manSyPlyUF]) {
                    $user = Utils::getUserbycondition(array('=ID' =>$arFields[manSyPlyUF]));
                    if($user) {
                        $PROPS['managerplyemail'] = $user['EMAIL'];
                    }
                }
                if($arFields[manLamUF]) {
                    $user = Utils::getUserbycondition(array('=ID' =>$arFields[manLamUF]));
                    if($user) {
                        $PROPS['managerlamartyemail'] = $user['EMAIL'];
                    }
                }
                if($arFields[marketinUF]) {
                    $PROPS['ismarket'] = $arFields[marketinUF];
                }
                if($PROPS) {
                    /*foreach($PROPS as $key=>$item) {
                        $data = [
                            'ACTIVE' => 'Y',
                            'NAME' => $key,
                            'PROPERTY_VALUES' => [
                                'NOVOE_ZNACHENIE'=> $item,
                                'KOMPANIYA' => $companyid,
                                'DATA_IZMENENIYA' => ConvertTimeStamp(time(), "FULL") 
                            ]
                        ];
                        $id = Utils::createIBlockElement(makeexportIB, $data);
                    }*/
                }
                
                // старый вариант - на каждый файл
                /*$arFilter = [
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
                    if($arFields['TITLE']) {
                        $export = true;
                        $root->Contragent->org = $arFields['TITLE'];
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
                    if($arFields[marketnameUF]) {
                        $export = true;
                        $root->Contragent->name1 = $arFields[marketnameUF];
                    }
                    if($arFields[marketthroughnameUF]) {
                        $export = true;
                        $root->Contragent->name2 = $arFields[marketthroughnameUF];
                    }
                    
                    if($arFields[dealerSyPlyUF]) {
                        $export = true;
                        $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arFields[dealerSyPlyUF]], ['NAME'=>'desc']));
                        if($ibid) {
                            $root->Contragent->dealerply = $ibid['NAME'];
                        }
                    }
                    if($arFields[dealerLamUF]) {
                        $export = true;
                        $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$arFields[dealerLamUF]], ['NAME'=>'desc']));
                        if($ibid) {
                            $root->Contragent->daelerlamarty = $ibid['NAME'];
                        }
                    }
                    if($arFields[marketUF]) {
                        $export = true;
                        $fieldval = Utils::getEnumvalue(marketiID, $arFields[marketUF], 'value');
                        $root->Contragent->market = $fieldval;
                    }
                    if($arFields[statusdealUF]) {
                        $export = true;
                        $root->Contragent->daelerlamarty2 = $arFields[statusdealUF];
                    }
                    if($arFields[partncodeUF]) {
                        $export = true;
                        $root->Contragent->partner = $arFields[partncodeUF];
                    }
                    if($arFields[furnitcompUF]) {
                        $export = true;
                        $root->Contragent->mebel = $arFields[furnitcompUF];
                    }
                    if($arFields[eng1UF]) {
                        $export = true;
                        $root->Contragent->name1eng = $arFields[eng1UF];
                    }
                    if($arFields[eng2UF]) {
                        $export = true;
                        $root->Contragent->name2eng = $arFields[eng2UF];
                    }
                    if($arFields[manSyPlyUF]) {
                        $export = true;
                        $user = Utils::getUserbycondition(array('=ID' =>$arFields[manSyPlyUF]));
                        $root->Contragent->managerplyemail = $user['EMAIL'];
                    }
                    if($arFields[manLamUF]) {
                        $export = true;
                        $user = Utils::getUserbycondition(array('=ID' =>$arFields[manLamUF]));
                        $root->Contragent->managerplyemail = $user['EMAIL'];
                    }
                    if($arFields[marketinUF]) {
                        $export = true;
                        $root->Contragent->ismarket = $arFields[marketinUF];
                    }
                    if($export) {
                        $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("m.d.y").'_'.date("H.i.s").'_'.'companyupdate.xml');
                    }
                }*/
            }
        }
    }

    public static function onEntityDetailsTabsInitialized(\Bitrix\Main\Event $event)
    {
        $MODULE_ID = 'sfz.custom';
        $entityID = $event->getParameter('entityID');
        \Bitrix\Main\Diag\Debug::writeToFile($entityID, "eid".date("d.m.Y G.i.s"), "__stzexp.log");
        $entTypeid = $event->getParameter('entityTypeID');
        \Bitrix\Main\Diag\Debug::writeToFile($entTypeid, "eid".date("d.m.Y G.i.s"), "__stzexp.log");
        $tabs = $event->getParameter('tabs');
        $tabs[] = [
            'id' => 'custom',
            'name' => Loc::getMessage($MODULE_ID.'_companies'),
            'enabled' => true
        ];

        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
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
