<?php

namespace iTrack\BpExtension\EventHandlers;

use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class Main
{
    public static function onProlog()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'main',
            'OnEpilog',
            ['\iTrack\BpExtension\EventHandlers\Main','onEpilog']
        );
		$eventManager->addEventHandler(
			'bizproc',
			'OnTaskAdd',
			['\iTrack\BpExtension\EventHandlers\Bizproc','onTaskAdd']
		);

        \CJSCore::RegisterExt('itrack_crm_bp_ext',[
            'js' => '/local/js/itrack.bpextension/itrack_crm_bp_ext/script.js',
            'css' => '/local/js/itrack.bpextension/itrack_crm_bp_ext/style.css'
        ]);
    }

    public static function onEpilog()
    {
        $urlTemplates = [
            'lead_detail' => ltrim(Option::get('crm', 'path_to_lead_details', '', SITE_ID), '/'),
            'deal_detail' => ltrim(Option::get('crm', 'path_to_deal_details', '', SITE_ID), '/'),
            'contact_detail' => ltrim(Option::get('crm', 'path_to_contact_details', '', SITE_ID), '/'),
            'company_detail' => ltrim(Option::get('crm', 'path_to_company_details', '', SITE_ID), '/')
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
            }
        }

        if(!empty($type)) {
            if($type === 'detail') {
                \CJSCore::init('itrack_crm_bp_ext');
                \Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/bizproc/tools.js');
                $asset = Asset::getInstance();
                $asset->addString('<script>BX.ready(function () {BX.iTrack.Crm.BpExt.init();});</script>');
            }
        }
    }
}