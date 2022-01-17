<?php
namespace sfz\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use SFZ\Custom\Helpers\Utils;

class Signal extends Controller
{
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