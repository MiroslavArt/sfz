<?php

namespace SFZ\Custom\Exchange;

use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;

class ExportImport
{
    public function __construct()
    {
        Loader::includeModule('crm');
    }

    public static function parseCompanyXML()
    {        
        if (file_exists($_SERVER['DOCUMENT_ROOT'].rootXML)) {
            $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].rootXML);
            $contr = $xml->Contragent;
            foreach($contr as $element) {
                    //$elementatr = $element->attributes();\
                    $newel = Utils::xml2array($element);
                    echo "<pre>";
                    //print_r($element);
                    print_r($newel['@attributes']['id']);
                    //print_r(xmlstring2array($element));
                    echo "</pre>";
            }
        } else {
            exit('Не удалось открыть файл'.rootXML);
        }
        
        return '\SFZ\Custom\Exchange\ExportImport::parseCompanyXML();';
    }
}