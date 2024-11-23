<?php
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/vendor/autoload.php')) {
    require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/vendor/autoload.php');
}

if(Bitrix\Main\Loader::includeModule('sfz.custom')) {
    \SFZ\Custom\Application::init();
}

require_once dirname(__DIR__) ."/standard_classes_replacement/StandardClassesReplacementAutoloader.php";
StandardClassesReplacementAutoloader::RegisterAutoloadFunction();
