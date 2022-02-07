<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Crm\Service;

class Iblock
{
    public static function OnAfterIBlockElementAdd(&$arFields)
    {
        if($arFields['IBLOCK_ID']==PLYWOODIB) {
            $manfield = TYPE2UFMANSYPLY; 
        } elseif($arFields['IBLOCK_ID']==LAMARTYIB) {
            $manfield = TYPE2UFMANLAM; 
        }
        if($manfield) {
            $element = Utils::getIBlockElementsByConditions($arFields['IBLOCK_ID'], ['ID'=>$arFields['ID']]);
            $manager = $element['PROPERTIES']['SOTRUDNIK']['VALUE'];
            $company = $element['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE']; 
            if($manager && $company) {
                self::updatethrougcompany($companyid, $manfield, $manager); 
            }
        }
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        if($arFields['IBLOCK_ID']==PLYWOODIB) {
            $manfield = TYPE2UFMANSYPLY; 
        } elseif($arFields['IBLOCK_ID']==LAMARTYIB) {
            $manfield = TYPE2UFMANLAM; 
        }
        if($manfield) {
            $element = Utils::getIBlockElementsByConditions($arFields['IBLOCK_ID'], ['ID'=>$arFields['ID']]);

        }
    }

    private static function updatethrougcompany($companyid, $manfield, $manager) {
        $factory = Service\Container::getInstance()->getFactory($typeid);
        $items = $factory->getItems([
            'select' => [],
            'filter' => ['ID'=>TYPE2ID]
        ]);
        $item = current($items); 
        if($item) {
            $item->set($manfield, $manager);
            $operation = $factory->getUpdateOperation($item);
            $operation
                ->disableCheckFields()
                ->disableBizProc()
                ->disableCheckAccess()
            ;
            $updateResult = $operation->launch();	
        }
    }
}