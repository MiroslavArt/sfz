<?php

namespace SFZ\Custom\EventHandlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use SFZ\Custom\Helpers\Utils;

class Socialnetwork {
    public static function OnFillSocNetFeaturesList(&$arSocNetFeaturesSettings)
    {
        $arSocNetFeaturesSettings["accidents"] = array(
            "FeatureName" => "Карта несчастных случаев",
            "allowed" => array(SONET_ENTITY_USER, SONET_ENTITY_GROUP),
            "operations" => array(
               "write" => array(SONET_ENTITY_USER => SONET_RELATIONS_TYPE_NONE, SONET_ENTITY_GROUP => SONET_ROLES_MODERATOR),
               "view" => array(SONET_ENTITY_USER => SONET_RELATIONS_TYPE_ALL, SONET_ENTITY_GROUP => SONET_ROLES_USER),
            ),
            "minoperation" => "view",
            "title" => "Карта несчастных случаев"
         );
    }
    public static function OnFillSocNetMenu(&$arResult)
    {
        \Bitrix\Main\Diag\Debug::writeToFile($arResult, "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        // Достуна для показа
        if(array_key_exists("accidents", $arResult["ActiveFeatures"])) {
            $arResult["CanView"]["accidents"] = true;
            // Ссылка закладки
            $arResult["Urls"]["accidents"] = \CComponentEngine::MakePathFromTemplate("/workgroups/group/#group_id#/accidents/", array("group_id" => $arResult["Group"]["ID"]));
            // Название закладки
            $arResult["Title"]["accidents"] = "Карта несчастных случаев";
            \Bitrix\Main\Diag\Debug::writeToFile($arResult, "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        }
        else {
            $arResult["CanView"]["accidents"] = false;
        }
    }
    public static function OnParseSocNetComponentPath(&$arUrlTemplates, &$arCustomPagesPath)
    {
        // Шаблон адреса страницы
        $arUrlTemplates["accidents"] = "group/#group_id#/accidents/";
        // Путь относительно корня сайта,
        $arCustomPagesPath["accidents"] = "/local/page_templates/";
    }
}