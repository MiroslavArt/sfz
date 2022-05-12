<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$APPLICATION->SetTitle("Карта несчастных случаев");
$APPLICATION->IncludeComponent(
   "bitrix:socialnetwork.group_menu",
   "",
   Array(
      "GROUP_VAR" => $arResult["ALIASES"]["group_id"],
      "PAGE_VAR" => $arResult["ALIASES"]["page"],
      "PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
      "PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
      "PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
      "PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
      "PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
      "PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
      "PATH_TO_GROUP_REQUESTS_OUT" => $arResult["PATH_TO_GROUP_REQUESTS_OUT"],
      "PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
      "PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"],
      "PATH_TO_GROUP_PHOTO" => $arResult["PATH_TO_GROUP_PHOTO"],
      "PATH_TO_GROUP_FORUM" => $arResult["PATH_TO_GROUP_FORUM"],
      "PATH_TO_GROUP_CALENDAR" => $arResult["PATH_TO_GROUP_CALENDAR"],
      "PATH_TO_GROUP_FILES" => $arResult["PATH_TO_GROUP_FILES"],
      "PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
      "GROUP_ID" => $arResult["VARIABLES"]["group_id"],
      "PAGE_ID" => "group_accidents",
   ),
   $component
);
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.smart.filter", 
	"sfz_map1
	", 
	array(
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"CONVERT_CURRENCY" => "N",
		"DISPLAY_ELEMENT_COUNT" => "Y",
		"FILTER_NAME" => "arrFilter",
		"FILTER_VIEW_MODE" => "horizontal",
		"HIDE_NOT_AVAILABLE" => "N",
		"IBLOCK_ID" => INCIB,
		"IBLOCK_TYPE" => "lists",
		"PAGER_PARAMS_NAME" => "arrPager",
		"POPUP_POSITION" => "left",
		"PREFILTER_NAME" => "smartPreFilter",
		"PRICE_CODE" => array(
		),
		"SAVE_IN_SESSION" => "N",
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"SECTION_DESCRIPTION" => "-",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_TITLE" => "-",
		"SEF_MODE" => "N",
		"SEF_RULE" => "",
		"SMART_FILTER_PATH" => "",
		"TEMPLATE_THEME" => "wood",
		"XML_EXPORT" => "N",
		"COMPONENT_TEMPLATE" => "sfz_map"
	),
	false
);?>
<?
$APPLICATION->IncludeComponent(
   "sfz:sfz.accidents.map",
   "",
   Array(
	   "FILTER" => $GLOBALS['arrFilter']
   ),
   $component
);
?>