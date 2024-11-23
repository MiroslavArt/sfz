<?php

namespace SFZ\Custom;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Main\DI;


class Application
{


    public static function init()
    {
        self::setConstants();
        self::initCss();
        //self::initJsHandlers();
        self::initEventHandlers();
        self::initFactorySubstitute(); 
    }

    protected static function setConstants() {   
        define('hidethema', \COption::GetOptionString('sfz.custom', 'main_hidethema'));
        define('FIREDEPT', \COption::GetOptionString('sfz.custom', 'main_firedept'));
        define('BOOKINGGROUP', \COption::GetOptionString('sfz.custom', 'main_bookinggroup'));
        define('IBBIRTHD', \COption::GetOptionString('sfz.custom', 'main_birthdayIB'));
        define('IBCONGBIRTHD', \COption::GetOptionString('sfz.custom', 'main_birthdaycongrIB'));
        define('EXTFUNCT', \COption::GetOptionString('sfz.custom', 'main_extfunct'));
        define('rootXML', \COption::GetOptionString('sfz.custom', 'company_rootXML'));
        define('importfileXML', \COption::GetOptionString('sfz.custom', 'company_importfileXML'));
        define('makeexportXML', \COption::GetOptionString('sfz.custom', 'company_makeexportXML'));
        define('makeexportIB', \COption::GetOptionString('sfz.custom', 'company_makeexportIB'));
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
        define('STATUSDEALUFID', \COption::GetOptionString('sfz.custom', 'company_statusdealUFID'));
        define('manLamUF', \COption::GetOptionString('sfz.custom', 'company_manLamUF'));
        define('manSyPlyUF', \COption::GetOptionString('sfz.custom', 'company_manSyPlyUF'));
        define('partncodeUF', \COption::GetOptionString('sfz.custom', 'company_partncodeUF'));
        define('furnitcompUF', \COption::GetOptionString('sfz.custom', 'company_furnitcompUF'));
        define('eng1UF', \COption::GetOptionString('sfz.custom', 'company_eng1UF'));
        define('eng2UF', \COption::GetOptionString('sfz.custom', 'company_eng2UF'));
        define('archiveUF', \COption::GetOptionString('sfz.custom', 'company_archiveUF'));
        define('marketinUF', \COption::GetOptionString('sfz.custom', 'company_marketinUF'));
        define('commdir', \COption::GetOptionString('sfz.custom', 'company_commdir'));
        define('CHECKLEAD', \COption::GetOptionString('sfz.custom', 'company_checklead'));
        define('WFID', \COption::GetOptionString('sfz.custom', 'company_wfid'));
        define('LEADSTAGE', \COption::GetOptionString('sfz.custom', 'company_leadstage'));
        define('SYPLYNUM', \COption::GetOptionString('sfz.custom', 'company_syplynumbers'));
        define('LAMNUM', \COption::GetOptionString('sfz.custom', 'company_lamartynumbers'));
        define('CONTRACTACTIVATE', \COption::GetOptionString('sfz.custom', 'payrequests_contractactivate'));
        define('TYPECODE', \COption::GetOptionString('sfz.custom', 'payrequests_typecode'));
        define('contractuf', \COption::GetOptionString('sfz.custom', 'payrequests_contractuf'));
        define('contractIB', \COption::GetOptionString('sfz.custom', 'payrequests_contractIB'));
        define('companypropIB', \COption::GetOptionString('sfz.custom', 'payrequests_companypropIB'));
        define('companypropIB', \COption::GetOptionString('sfz.custom', 'payrequests_companypropIB'));
        define('TCTABACTIVATE', \COption::GetOptionString('sfz.custom', 'throughcompanies_tabactivate'));
        define('COMPANYUF1', \COption::GetOptionString('sfz.custom', 'throughcompanies_companyuf1'));
        define('COMPANYUF2', \COption::GetOptionString('sfz.custom', 'throughcompanies_companyuf2'));
        define('TYPE1ID', \COption::GetOptionString('sfz.custom', 'throughcompanies_type1id'));
        define('TYPE1UFENG', \COption::GetOptionString('sfz.custom', 'throughcompanies_type1ufeng'));
        define('TYPE2ID', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2id'));
        define('TYPE2UFENG', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2ufeng'));
        define('TYPE2UFMANSYPLY', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2ufmansyply'));
        define('TYPE2UFMANLAM', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2ufmanlam'));
        define('TYPE2UFMANSYPLYD', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2ufmansyplydept'));
        define('TYPE2UFMANLAMD', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2ufmanlamdept'));
        define('TYPE2UFACTIVE', \COption::GetOptionString('sfz.custom', 'throughcompanies_type2active'));
        define('PLYWOODIB', \COption::GetOptionString('sfz.custom', 'throughcompanies_plywoodIB'));
        define('LAMARTYIB', \COption::GetOptionString('sfz.custom', 'throughcompanies_lamartyIB'));
        define('PLYWOODSUBST', \COption::GetOptionString('sfz.custom', 'throughcompanies_plywoodSUBST'));
        define('LAMARTYSUBST', \COption::GetOptionString('sfz.custom', 'throughcompanies_lamartySUBST'));
        define('PLYWOODSUBSTUNDO', \COption::GetOptionString('sfz.custom', 'throughcompanies_plywoodSUBSTUNDO'));
        define('LAMARTYSUBSTUNDO', \COption::GetOptionString('sfz.custom', 'throughcompanies_lamartySUBSTUNDO'));
        define('WFFILTER', \COption::GetOptionString('sfz.custom', 'throughcompanies_wfFilter'));
        define('WFCODEFILTER', \COption::GetOptionString('sfz.custom', 'throughcompanies_wfcodeFilter'));
        //define('KUBIB', \COption::GetOptionString('sfz.custom', 'infokiosk_kubikIB'));
        define('INCIB', \COption::GetOptionString('sfz.custom', 'infokiosk_incidentsIB'));
        define('HORIZON', \COption::GetOptionString('sfz.custom', 'infokiosk_cellshorizontal'));
        define('VERTU', \COption::GetOptionString('sfz.custom', 'infokiosk_cellsvertical'));
    }

    protected static function initJsHandlers()
    {

    }

    public static function initCss()
    {
        global $APPLICATION;
        // add custom css for crm forms
        $APPLICATION->SetAdditionalCSS("/local/css/sfz.css");
    }

    public static function initEventHandlers()
    {
        $eventManager = EventManager::getInstance();
        //$eventManager->addEventHandler('crm','onAfterCrmCompanyUpdate', ['\SFZ\Custom\EventHandlers\Crm','onAfterCrmCompanyUpdate']);
        //$eventManager->addEventHandler('crm','onBeforeCrmCompanyUpdate', ['\SFZ\Custom\EventHandlers\Crm','onBeforeCrmCompanyUpdate']);
        //$eventManager->addEventHandler('crm','onAfterRequisiteAdd', ['\SFZ\Custom\EventHandlers\Crm','onAfterRequisiteAdd']);
        
        // хандлер таба - пока деактивен
        $eventManager->addEventHandler('crm','onEntityDetailsTabsInitialized', ['\SFZ\Custom\EventHandlers\Crm','onEntityDetailsTabsInitialized']);
        $eventManager->addEventHandler('main','OnProlog', ['\SFZ\Custom\EventHandlers\Main','onProlog']);
        $eventManager->addEventHandler('main','OnAfterUserUpdate', ['\SFZ\Custom\EventHandlers\Main','OnAfterUserUpdate']);
        //$eventManager->addEventHandler("report", "onAnalyticPageCollect", ['\SFZ\Custom\EventHandlers\Crm',"onAnalyticPageCollect"]);
        
        //$eventManager->addEventHandler('main','onGetUserFieldValues', ['\SFZ\Custom\EventHandlers\Main','onGetUserFieldValues']);
        
        $eventManager->addEventHandler('iblock','OnAfterIBlockElementAdd', ['\SFZ\Custom\EventHandlers\Iblock','OnAfterIBlockElementAdd']);
        $eventManager->addEventHandler('iblock','OnAfterIBlockElementUpdate', ['\SFZ\Custom\EventHandlers\Iblock','OnAfterIBlockElementUpdate']);
        //$eventManager->addEventHandler('iblock','OnAfterIBlockElementDelete', ['\SFZ\Custom\EventHandlers\Iblock','OnAfterIBlockElementDelete']);
        // старый хандлер эпилога
        //$eventManager->addEventHandler('main','OnEpilog', ['\SFZ\Custom\EventHandlers\Main','onEpilog']);
        if(EXTFUNCT=="Y") {
            $eventManager->addEventHandler('socialnetwork','OnFillSocNetFeaturesList', ['\SFZ\Custom\EventHandlers\Socialnetwork','OnFillSocNetFeaturesList']);
            $eventManager->addEventHandler('socialnetwork','OnFillSocNetMenu', ['\SFZ\Custom\EventHandlers\Socialnetwork','OnFillSocNetMenu']);
            $eventManager->addEventHandler('socialnetwork','OnParseSocNetComponentPath', ['\SFZ\Custom\EventHandlers\Socialnetwork','OnParseSocNetComponentPath']);
        }
    }

    public static function initFactorySubstitute()  
    {
       if (makeexportXML=='Y')
       {
            if (\Bitrix\Main\Loader::includeModule('crm'))
            {                   
                $type = new \SFZ\Custom\EventHandlers\Type(); 
                // here we change the container
                DI\ServiceLocator::getInstance()->addInstance('crm.service.container', $type);
            }
        }
        
    }

    public static function log($msg, $file = 'main.log')
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/logs/'.$file, date(DATE_COOKIE).': '.$msg."\n", FILE_APPEND);
    }
}