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
                        $PROPS['tel'] = $phone;
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
                    foreach($PROPS as $key=>$item) {
                        $data = [
                            'ACTIVE' => 'Y',
                            'NAME' => $key,
                            'PROPERTY_VALUES' => [
                                'NOVOE_ZNACHENIE'=> $item,
                                'KOMPANIYA' => $companyid,
                                'DATA_IZMENENIYA' => ConvertTimeStamp(time(), "FULL") 
                            ]
                        ];
                        $id = Utils::createIBlockElement(makeexportIB, $data, []);
                    }
                }
            }
        }
    }

    public static function onEntityDetailsTabsInitialized(\Bitrix\Main\Event $event)
    {
        
        $MODULE_ID = 'sfz.custom';
        $entityID = $event->getParameter('entityID');
        $entTypeid = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');
        if($entTypeid==TYPE1ID) {
            $tabs[] = [
                'id' => 'custom',
                'name' => Loc::getMessage($MODULE_ID.'_companies'),
                'enabled' => true,
                'loader' => [
                    'serviceUrl' => '/bitrix/components/bitrix/crm.deal.list/lazyload.ajax.php?site'.SITE_ID.'&'.bitrix_sessid_get(),
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'DEAL_COUNT' => 20,
                            'INTERNAL_FILTER' => [
                                'COMPANY_ID' => 4263 
                            ],
                            'INTERNAL_CONTEXT' => [
                                'COMPANY_ID' => 4263 
                            ],
                            'GRID_ID_SUFFIX' => [
                                'COMPANY_DETAILS'
                            ],
                            'TAB_ID' =>  'tab_deal',
                            'NAME_TEMPLATE' => '#LAST_NAME# #NAME# #SECOND_NAME#',
                            'ENABLE_TOOLBAR' => 1,
                            'PRESERVE_HISTORY' => 1,
                            'ADD_EVENT_NAME' => 'CrmCreateDealFromCompany'
                        ]
                    ]
                ]
            ];
        }
        \Bitrix\Main\Diag\Debug::writeToFile($tabs, "tabs".date("d.m.Y G.i.s"), "__stzexp.log");
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
     
    public static function onBeforeCrmDealUpdate(&$arFields)
    {
        
    }

    public static function onBeforeCrmDealAdd(&$arFields)
    {
        
    }

    
}
