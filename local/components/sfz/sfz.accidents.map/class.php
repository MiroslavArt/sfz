<?php

use SFZ\Custom\Helpers\Utils;
use \Bitrix\Main\Loader;

class AccMap extends CBitrixComponent
{
    private $filtervalues; 
    private $kubikvalues;
    private $size;
    private $rowidprop;
    private $columnidprop;
    private $kartidprop; 
    /*private $arLost;
    private $lostId;
    private $companyId;
    private $contractId;
    private $errors;
    private $userRole;*/

    public function onPrepareComponentParams($arParams)
    {
        // параметры для фильтров тут
        //$this->companyId = $arParams['CLIENT_ID'];
        //$this->contractId = $arParams['CONTRACT_ID'];
        $this->filtervalues = $arParams['FILTER'];

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        $arResult['testparam'] = 'Здесь будет отображение карты несчастных случаев';

        $this->kubikvalues = $this->getKubikvalues();
        $this->size = $this->getSize();
        //\Bitrix\Main\Diag\Debug::writeToFile($GLOBALS['arrFilter'], "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        \Bitrix\Main\Diag\Debug::writeToFile($this->filtervalues, "filter".date("d.m.Y G.i.s"), "__debug.log");
        \Bitrix\Main\Diag\Debug::writeToFile($this->size, "size".date("d.m.Y G.i.s"), "__debug.log");
        $this->includeComponentTemplate();
    }

    private function getKubikvalues() {
        return Utils::getIBlockElementsByConditions(KUBIB, ["ACTIVE"=>'Y', "!PROPERTY_RYAD"=>false, 
            "!PROPERTY_KOLONKA"=>false], [], [], false);
        
    }
    

    private function prepareProperties() {
        Loader::includeModule('iblock');
        $res = \CIBlockProperty::GetList([],['IBLOCK_ID'=>KUBIB]);
        while($uid = $res->fetch()) {
            if($uid['CODE']=='RYAD') {
                $this->rowidprop = $uid['ID'];
            } elseif($uid['CODE']=='KOLONKA') {
                $this->columnidprop = $uid['ID'];
            } elseif($uid['CODE']=='KARTINKA') {
                $this->kartidprop = $uid['ID'];
            }
        }
    }
    
    private function getSize() {
        $size = ['length' => 0, 'height' => 0];
            
        $lengths = [];
        $heights = []; 

        foreach($this->kubikvalues as $item) {
            array_push($lengths, $item['PROPERTY_'.$this->columnidprop]);
            array_push($heights, $item['PROPERTY_'.$this->rowidprop]);
        }
        
        $size['length'] = intval(max(array_unique($lengths))); 
        $size['height'] = intval(max(array_unique($heights))); 
        return $size;

    }

    //use SFZ\Custom\Helpers\Utils;
    //use \Bitrix\Main\Loader;
    
    //$kub = Utils::getIBlockElementsByConditions(KUBIB, ["ACTIVE"=>'Y', "!PROPERTY_RYAD"=>false, 
    //"!PROPERTY_KOLONKA"=>false]);
    //Loader::includeModule('iblock');
    //$res = \CIBlock::GetProperties(KUBIB, Array(), Array("CODE"=>"SRC"));
    
    //echo "<pre>";
    //print_r($res->Fetch());
    //echo "</pre>";
}