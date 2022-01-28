<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;

class Iblock
{
    public static function OnBeforeIBlockElementAdd(&$arFields)
    {
        //\Bitrix\Main\Diag\Debug::writeToFile($arFields, "аа".date("d.m.Y G.i.s"));
        //global $APPLICATION;
        //$APPLICATION->throwException("Косяк.");
        //return false;
    }

    
}