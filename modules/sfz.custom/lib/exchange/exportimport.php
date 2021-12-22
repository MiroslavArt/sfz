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
        $obEnum = new \CUserFieldEnum; 
        $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => 114)); 
        //print_r($rsEnum);
        while($arEnum = $rsEnum->Fetch()){
                //if($arEnum["VALUE"]==$value){ 
                //$idvalue=$arEnum["ID"];
                //}
            print_r($arEnum); 
        } 
        
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/pub/galaktikadata/b24_contragents.xml')) {
            $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'/pub/galaktikadata/b24_contragents.xml');
            $contr = $xml->Contragent;
            foreach($contr as $element) {
                    //$elementatr = $element->attributes();\
                    $newel = xml2array($element);
                    echo "<pre>";
                    //print_r($element);
                    print_r($newel['@attributes']['id']);
                    //print_r(xmlstring2array($element));
                    echo "</pre>";
            }
        } else {
            exit('Не удалось открыть файл test.xml.');
        }
        
        function xml2array ( $xmlObject, $out = array () )
        {
            foreach ( (array) $xmlObject as $index => $node )
                $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
        
            return $out;
        }
        return '\SFZ\Custom\Exchange\ExportImport::parseCompanyXML;';
    }
}