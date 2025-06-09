<?php

namespace Notamedia\Analytics\EventHandlers;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\DI;
use Bitrix\Crm\Service\Factory;
use Bitrix\Crm\Integration\Report\Filter\Deal\SalesDynamicFilter;
use Bitrix\Crm\Integration\Report\Dashboard\Sales\SalesDynamic;
use Bitrix\Crm\Integration\Report\Dashboard\MyReports;

class Crm
{
    public static function OnAfterCrmDealUpdate(&$arFields)
    {
        
        if(Loader::includeModule('crm')) {
            $arFilter = [
                "ID" => $arFields['ID'],
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "ID", "CATEGORY_ID"
            ];
            $res = \CCrmDeal::GetListEx([], $arFilter, false, false, $arSelect)->Fetch();
            \CPullStack::AddShared(['module_id' => 'crm', 'command' => 'crm_sfz_pipeline_update', 'params' => ['CATEGORY_ID' => $res["CATEGORY_ID"]]]);
        }
    }
}