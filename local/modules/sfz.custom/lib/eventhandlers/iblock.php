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
        self::processupdate($arFields); 
    }

    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        self::processupdate($arFields);        
    }

    public static function OnBeforeIBlockElementDelete(&$arFields)
    {
        self::processupdate($arFields. false);        
    }

    private static function processupdate($arFields, $save = true) {
        if($arFields['IBLOCK_ID']==PLYWOODIB) {
            $manfield = TYPE2UFMANSYPLY; 
        } elseif($arFields['IBLOCK_ID']==LAMARTYIB) {
            $manfield = TYPE2UFMANLAM; 
        }
        if($manfield) {
            $element = current(Utils::getIBlockElementsByConditions($arFields['IBLOCK_ID'], ['ID'=>$arFields['ID']]));
            
            $manager = $element['PROPERTIES']['SOTRUDNIK']['VALUE']; 
            $companyid = $element['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE']; 
            $datefrom = $element['PROPERTIES']['DATA_SMENY_MENEDZHERA']['VALUE'];

            if($manager && $companyid) {
                $check = self::checkforupdate($arFields['IBLOCK_ID'], $companyid, $datefrom, $arFields['ID']);
                if($check) {
                    if(!$save) {
                       $manager = "";
                    }
                    
                    self::updatethrougcompany($companyid, $manfield, $manager); 
                }
            }
        }
    }

    private static function checkforupdate($iblockid, $companyid, $datefrom, $searchid) {
        $checkresult = false; 

        $fromdate = ConvertDateTime($datefrom, "Y-m-d"); 

        $checkelement = Utils::getIBlockElementsByConditions($iblockid, [">"."PROPERTY_DATA_SMENY_MENEDZHERA"=>$fromdate, 
            "=PROPERTY_SKVOZNAYA_KOMPANIYA_2"=>$companyid]);
        
        if(empty($checkelement)) {
            $checkresult = true;
        }
        return $checkresult; 
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