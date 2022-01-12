<?php

namespace SFZ\Custom;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;


class Application
{


    public static function init()
    {
        self::setConstants();
        //self::initCss();
        //self::initJsHandlers();
        self::initEventHandlers();
    }

    protected static function setConstants() {   
        define('rootXML', \COption::GetOptionString('sfz.custom', 'company_rootXML'));
        define('importfileXML', \COption::GetOptionString('sfz.custom', 'company_importfileXML'));
        define('makeexportXML', \COption::GetOptionString('sfz.custom', 'company_makeexportXML'));
        define('marketiID', \COption::GetOptionString('sfz.custom', 'company_marketiID'));
        define('marketUF', \COption::GetOptionString('sfz.custom', 'company_marketUF'));
        define('dealerIB', \COption::GetOptionString('sfz.custom', 'company_dealerIB'));
        define('dealerSyPlyUF', \COption::GetOptionString('sfz.custom', 'company_dealerSyPlyUF'));
        define('dealerLamUF', \COption::GetOptionString('sfz.custom', 'company_dealerLamUF'));
        define('marketnameUF', \COption::GetOptionString('sfz.custom', 'company_marketnameUF'));
        define('marketthroughnameUF', \COption::GetOptionString('sfz.custom', 'company_marketthroughnameUF'));
        define('idGalUF', \COption::GetOptionString('sfz.custom', 'company_idGalUF'));
        define('hashUF', \COption::GetOptionString('sfz.custom', 'company_hashUF'));
        define('statusdealUF', \COption::GetOptionString('sfz.custom', 'company_statusdealUF'));
        define('manLamUF', \COption::GetOptionString('sfz.custom', 'company_manLamUF'));
        define('manSyPlyUF', \COption::GetOptionString('sfz.custom', 'company_manSyPlyUF'));
        define('partncodeUF', \COption::GetOptionString('sfz.custom', 'company_partncodeUF'));
        define('furnitcompUF', \COption::GetOptionString('sfz.custom', 'company_furnitcompUF'));
        define('eng1UF', \COption::GetOptionString('sfz.custom', 'company_eng1UF'));
        define('eng2UF', \COption::GetOptionString('sfz.custom', 'company_eng2UF'));
        define('archiveUF', \COption::GetOptionString('sfz.custom', 'company_archiveUF'));
        define('marketinUF', \COption::GetOptionString('sfz.custom', 'company_marketinUF'));
        define('commdir', \COption::GetOptionString('sfz.custom', 'company_commdir'));
    }

    protected static function initJsHandlers()
    {

    }

    public static function initCss()
    {
        //global $APPLICATION;
        //$APPLICATION->SetAdditionalCSS("/local/css/itrack.css");
    }

    public static function initEventHandlers()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler('crm','onAfterCrmCompanyUpdate', ['\SFZ\Custom\EventHandlers\Crm','onAfterCrmCompanyUpdate']);
        $eventManager->addEventHandler('main','OnEpilog', ['\SFZ\Custom\EventHandlers\Main','onEpilog']);
        //$eventManager->addEventHandler('crm','OnBeforeCrmDealUpdate', ['\iTrack\Custom\EventHandlers\Crm','onBeforeCrmDealUpdate']);
        //$eventManager->addEventHandler('crm','OnBeforeCrmDealAdd', ['\iTrack\Custom\EventHandlers\Crm','OnBeforeCrmDealAdd']);
    }

    public static function log($msg, $file = 'main.log')
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/logs/'.$file, date(DATE_COOKIE).': '.$msg."\n", FILE_APPEND);
    }
}