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
        if(contractactivate == 'Y') {

            $urlTemplates = [
                'type_detail' => 'crm/type/'.typecode.'/details/#type_id#/'
            ];

            $page = \CComponentEngine::parseComponentPath('/', $urlTemplates, $arVars);
  
            if($page=='type_detail') {
                $typeid = $arVars['type_id'];
                $companyid = 'na'; 
                if($typeid) {
                    $typeval = Utils::getTypevalues ( typecode, $typeid );
                    $companyid = $typeval['COMPANY_ID'] ? $typeval['COMPANY_ID'] : 'na'; 
                }
                \CJSCore::init(['type_requests_filtercontract']);
                $asset->addString('<script>BX.ready(function () {BX.sfz.Type.RequestsFilterContract.init("'.contractuf.'", "'.$companyid.'");});</script>');
            }
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
}