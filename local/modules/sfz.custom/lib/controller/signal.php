<?php
namespace sfz\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Main\Loader;

class Signal extends Controller
{
    public function getSignalAction($signals)
    {
        Loader::includeModule('crm');
        
        $arFilter = [
            "=ID" => $signals,
            "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
        ];
        $arFilter = array_merge($arFilter, $filter);
        $arSelect = [
            "ID",
            "ACCOUNT_CURRENCY_ID",
            "OPPORTUNITY_ACCOUNT",
            "STATUS_ID"
        ];
        $res = \CCrmLead::GetListEx(Array(), $arFilter, false, false, $arSelect);
        $currencies = [];
        while($lead = $res->Fetch()) {
            $currencies[$lead['STATUS_ID']][$lead['ACCOUNT_CURRENCY_ID']] += $lead['OPPORTUNITY_ACCOUNT'];    
        }
        
        return $currencies;
    }
    
    public function getTypeclientAction($type)
    {

        /*use Bitrix\Main\Loader;
        use Bitrix\Crm\Service;
        Loader::includeModule('crm');
        echo CCrmOwnerType::ResolveID('DYNAMIC_147');

        $factory = Service\Container::getInstance()->getFactory('147');
        $sourceItemId = 1;
        $item = $factory->getItem($sourceItemId);
        //var_dump($item);
        echo "<pre>";
        print_r($item->getData());
        echo "</pre>";*/
        
        return $client;
    }
    
    
    public function getContractAction($companyid)
    {

        $selectcontract = Utils::getIBlockElementsByConditions(contractIB, ['PROPERTY_'.companypropIB=>$companyid]);
        $contractarr = []; 

        foreach($selectcontract as $item) {
            $contractarr[$item['ID']] = $item['NAME']; 
        }

        return $contractarr;
    }
}