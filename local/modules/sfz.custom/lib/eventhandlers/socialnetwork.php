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
}