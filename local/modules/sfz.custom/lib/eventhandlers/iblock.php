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
            \Bitrix\Main\Diag\Debug::writeToFile('here1', "dataexp".date("d.m.Y G.i.s"));
            $element = Utils::getIBlockElementsByConditions($arFields['IBLOCK_ID'], ['ID'=>$arFields['ID']]);
            \Bitrix\Main\Diag\Debug::writeToFile($element, "dataexp".date("d.m.Y G.i.s"));
            $manager = $element['PROPERTIES']['SOTRUDNIK']['VALUE'];
            $companyid = $element['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE']; 
            if($manager && $companyid) {
                \Bitrix\Main\Diag\Debug::writeToFile('here2', "dataexp".date("d.m.Y G.i.s"));
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
        $factory = Service\Container::getInstance()->getFactory(TYPE2ID);
        $items = $factory->getItems([
            'select' => [],
            'filter' => ['ID'=>$companyid]
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