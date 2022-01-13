<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;

class Main
{
    public static function onProlog()
    {
        /*$eventManager = EventManager::getInstance();
        $eventManager->addEventHandler('main','OnEpilog', ['\iTrack\Custom\EventHandlers\Main','onEpilog']);

        \CJSCore::RegisterExt('itrack_crm_detail_editor_ext',
            [
                'js' => '/local/js/itrack.custom/crm_detail_editor_ext/script.js',
                'css' => '/local/js/itrack.custom/crm_detail_editor_ext/style.css'
            ]
        );*/
    }

    public static function onEpilog()
    {
        
        global $USER;
        \CJSCore::init(['jquery', 'general_change_thema']);
        $asset = Asset::getInstance();
        $asset->addString('<script>BX.ready(function () {BX.sfz.General.ChangeThema.init("'.$USER->IsAdmin().'");});</script>');
        
        /*$urlTemplates = [
            'lead_detail' => ltrim(Option::get('crm', 'path_to_lead_details', '', SITE_ID), '/'),
            'deal_detail' => ltrim(Option::get('crm', 'path_to_deal_details', '', SITE_ID), '/'),
            'contact_detail' => ltrim(Option::get('crm', 'path_to_contact_details', '', SITE_ID), '/'),
            'company_detail' => ltrim(Option::get('crm', 'path_to_company_details', '', SITE_ID), '/'),
            'lead_kanban' => ltrim(Option::get('crm', 'path_to_lead_kanban', '', SITE_ID), '/'),
            'deal_kanban' => ltrim(Option::get('crm', 'path_to_deal_kanban', '', SITE_ID), '/'),
            'deal_kanban_category' => ltrim(Option::get('crm', 'path_to_deal_kanban', '', SITE_ID), '/').'category/#category_id#/',
            'contact_list' => ltrim(Option::get('crm', 'path_to_contact_list', '', SITE_ID), '/'),
            'company_list' => ltrim(Option::get('crm', 'path_to_company_list', '', SITE_ID), '/'),
            'tasks_list' => ltrim(Option::get('tasks', 'paths_task_user', '', SITE_ID), '/'),
        ];

        $page = \CComponentEngine::parseComponentPath('/', $urlTemplates, $arVars);
        $type = '';
        if($page !== false) {
            switch($page) {
                case 'lead_detail':
                case 'deal_detail':
                case 'contact_detail':
                case 'company_detail':
                    $type = 'detail';
                    break;
                case 'lead_kanban':
                case 'deal_kanban':
                case 'deal_kanban_category':
                    $type = 'kanban';
                    break;
                case 'contact_list':
                case 'company_list':
                    $type = 'list';
                    break;
            }
        }
        
        $asset = Asset::getInstance();

        if($page !== false && $page === 'deal_detail') {
            \CJSCore::init(['jquery', 'itrack_crm_detail_editor_ext']);
            $asset->addString('<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"></link>');
            $asset->addString('<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>');
            $asset->addString('<script>BX.ready(function () {BX.iTrack.Crm.DetailEditorExt.init();});</script>');
        }*/
    }
}