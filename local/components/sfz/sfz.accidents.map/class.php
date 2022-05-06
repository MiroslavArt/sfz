<?php

use SFZ\Custom\Helpers\Utils;

class AccMap extends CBitrixComponent
{
    private $filtervalues; 
    private $kubikvalues;
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
        //\Bitrix\Main\Diag\Debug::writeToFile($GLOBALS['arrFilter'], "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        \Bitrix\Main\Diag\Debug::writeToFile($this->filtervalues, "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        $this->includeComponentTemplate();
    }

    private function getKubikvalues() {
        return Utils::getIBlockElementsByConditions(KUBIB, ["ACTIVE"=>'Y', "!PROPERTY_RYAD"=>false, 
            "!PROPERTY_KOLONKA"=>false], [], [], ["PROPERTY_RYAD", "PROPERTY_KOLONKA"]);
        
    }
    
    
    private function getSize() {

    }


}