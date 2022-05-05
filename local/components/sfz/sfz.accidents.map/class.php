<?php

class AccMap extends CBitrixComponent
{
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
        //$this->lostId = $arParams['LOST_ID'];

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $arResult =& $this->arResult;

        $arResult['testparam'] = 'Здесь будет отображение карты несчастных случаев';

        $this->includeComponentTemplate();
    }

    private function getSize() {

    }


}