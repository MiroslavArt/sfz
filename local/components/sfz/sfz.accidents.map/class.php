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
    private $elemprop;
    private $yearprop;
    private $stepenprop; 
    private $descrprop; 
    private $dolzhnprop; 
    private $xcoordprop;
    private $ycoordprop; 
    private $accarr; 

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
        $arResult['height'] = VERTU;
        $arResult['length'] = HORIZON;
        $this->prepareProperties(); 
        //$this->kubikvalues = $this->getKubikvalues();
        //$this->size = $this->getSize();
        //\Bitrix\Main\Diag\Debug::writeToFile($GLOBALS['arrFilter'], "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        $this->accarr = $this->getAccidents(); 
        \Bitrix\Main\Diag\Debug::writeToFile($this->accarr, "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        $this->includeComponentTemplate();
    }

    private function getKubikvalues() {
        return Utils::getIBlockElementsByConditions(KUBIB, ["ACTIVE"=>'Y', "!PROPERTY_RYAD"=>false, 
            "!PROPERTY_KOLONKA"=>false], [], [], [], false);
    }

    private function getAccidents() {
        return Utils::getIBlockElementsByConditions(INCIB, [$this->filtervalues]);
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
        $res = \CIBlockProperty::GetList([],['IBLOCK_ID'=>INCIB]);
        while($uid = $res->fetch()) {
            if($uid['CODE']=='ELEMENT_KARTY') {
                $this->elemprop = $uid['ID'];
            } elseif($uid['CODE']=='OPISANIE_NESCHASTNOGO_SLUCHAYA') {
                $this->descrprop = $uid['ID'];
            } elseif($uid['CODE']=='STEPEN_TYAZHESTI') {
                $this->stepenprop = $uid['ID'];
            } elseif($uid['CODE']=='DOLZHNOSTI') {
                $this->stepenprop = $uid['ID'];
            } elseif($uid['CODE']=='GOD_NESCHASTNOGO_SLUCHAYA') {
                $this->yearprop = $uid['ID'];
            } elseif($uid['CODE']=='PORYADKOVYY_NOMER_YACHEYKI_PO_VERTIKALI') {
                $this->ycoordprop = $uid['ID'];
            } elseif($uid['CODE']=='PORYADKOVYY_NOMER_YACHEYKI_PO_GORIZONTALI') {
                $this->xcoordprop = $uid['ID'];
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

  
}