<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;

class Main
{
    public static function onProlog()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler('main','OnEpilog', ['\sfz\Custom\EventHandlers\Main','onEpilog']);
    }

    public static function onEpilog()
    {
        
        global $USER;
        //\CJSCore::init(['jquery', 'general_change_thema', 'type_requests_filtercontract']);
        \CJSCore::init(['jquery', 'general_change_thema']);
        $asset = Asset::getInstance();
        if(hidethema=="Y") {
            $asset->addString('<script>BX.ready(function () {BX.sfz.General.ChangeThema.init("'.$USER->IsAdmin().'");});</script>');
        }
        
        $urlTemplates = [
            'type_detail_request' => 'crm/type/'.TYPECODE.'/details/#type_id#/',
            'type_detail_throughcompany' => 'crm/type/'.TYPE2ID.'/details/#type_id#/',
            'room_booking' => 'calendar/rooms/',
            'lead_kanban' => ltrim(Option::get('crm', 'path_to_lead_kanban', '', SITE_ID), '/')
        ];

        $page = \CComponentEngine::parseComponentPath('/', $urlTemplates, $arVars);

        if($page=='type_detail_request' && CONTRACTACTIVATE == 'Y') {
            $typeid = $arVars['type_id'];
            $companyid = 'na'; 
            if($typeid) {
                $typeval = Utils::getTypevalues ( typecode, $typeid );
                $companyid = $typeval['COMPANY_ID'] ? $typeval['COMPANY_ID'] : 'na'; 
            }
            \CJSCore::init(['type_requests_filtercontract']);
            $asset->addString('<script>BX.ready(function () {BX.sfz.Type.RequestsFilterContract.init("'.contractuf.'", "'.$companyid.'");});</script>');
        } elseif($page=='type_detail_throughcompany') {
            $typeid = $arVars['type_id'];
            \CJSCore::init(['type_throughcomp_hidemanager']);
            if($typeid==0) {
                $mode = 'hidesection'; 
            } else {
                $mode = 'hideedit';
            }
            //$ufarr = json_encode([TYPE2UFMANSYPLY, TYPE2UFMANLAM]);
            $asset->addString('<script>BX.ready(function () {BX.sfz.Type.HideManagerEdit.init("'.$mode.'", "'.TYPE2UFMANSYPLY.'", "'.TYPE2UFMANLAM.'");});</script>');
        } elseif($page=='room_booking') {
            // выбираем группу
            $arGroups = \CUser::GetUserGroup($USER->GetID());
            $hidegroup = 1;
            if($USER->IsAdmin()) {
                $hidegroup = 0;
            } else {
                foreach($arGroups as $group) {
                    if($group==BOOKINGGROUP) {
                        $hidegroup = 0;
                        break;
                    }
                }
            }
            
            \CJSCore::init(['calendar_hidebooking']);
            $asset->addString('<script>BX.ready(function () {BX.sfz.Calendar.HideBooking.init("'.$hidegroup.'");});</script>');
        } elseif($page=='lead_kanban') {
            \CJSCore::init(['crm_kanban']);
            $asset->addString('<script>BX.ready(function () {BX.sfz.crm.kanban.init();});</script>');
        }
    }

    public static function onGetUserFieldValues(\Bitrix\Main\Event $event)
    {
        $result = new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS);

        $entityId = $event->getParameter('entityId');
        $userFields = $event->getParameter('userFields');
        $value = $event->getParameter('value');

        return $result;
     
    }

    public static function OnAfterUserUpdate(&$arFields)
    {
        $user = \Bitrix\Main\UserTable::getList(array(
            'filter' => array(
                '=ID' => $arFields['ID']
            ),
            'limit'=>1,
            'select'=>array('*','UF_*'),
        ))->Fetch();

        if($user['ACTIVE']=='N' && FIREDEPT) {
            $update = true; 
            if($user['UF_DEPARTMENT']) {
                foreach($user['UF_DEPARTMENT'] as $item) {
                    if($item==FIREDEPT) {
                        $update = false;
                    }
                }
            }
            if($update) {
                $userobj = new \CUser;
                $fields = [
                    'UF_DEPARTMENT' => [FIREDEPT]
                ];
                $userobj->Update($user['ID'], $fields);
            }
        }
        
        
    }
}