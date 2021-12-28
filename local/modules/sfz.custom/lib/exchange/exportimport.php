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
        Loader::includeModule('crm');
        $impfile = $_SERVER['DOCUMENT_ROOT'].rootXML.'/'.importfileXML;
        if (file_exists($impfile)) {
            $xml = simplexml_load_file($impfile);
            $contr = $xml->Contragent;
            $test = 0; 
            $company=new \CCrmCompany();
            $requisiteEntity = new \Bitrix\Crm\EntityRequisite();
            foreach($contr as $element) {
                $newel = Utils::xml2array($element);

                $idgal = $newel['@attributes']['id'];
                $title = $newel['org'];

                if($idgal && $title) {

                    $arFilter = [
                        idGalUF => $idgal, //выбираем определенную сделку по ID
                        "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
                    ];
                    $arSelect = [
                        "*",
                        "UF_*"
                    ];
                    $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
                    $arCompany = $res->fetch();

                    if(empty($arCompany)) {

                        $arNewCompany = self::preparecompanydata($newel);
                        $companyID = $company->Add($arNewCompany);
                        
                        if($companyID) {
                            $requisiteFields=[
                                "ENTITY_TYPE_ID"=>4, /*реквизит для компании*/
                                "ENTITY_ID"=>$companyID, /* ид нашей созданной компании*/
                                "NAME"=>"Организация",
                                "PRESET_ID"=>1,
                                "RQ_COMPANY_NAME"=>$title,
                                "RQ_COMPANY_FULL_NAME"=>$title
                            ];

                            if($newel['inn']) {
                                $requisiteFields['RQ_INN'] = $newel['inn']; 
                            }

                            if($newel['kpp']) {
                                $requisiteFields['RQ_KPP'] = $newel['kpp']; 
                            }

                            
                            $resultRequisit = $requisiteEntity->add($requisiteFields);
                        }
                    } else {
                        if($arCompany[hashUF]!=$newel['row_hash']) {
                            $arUpdateCompany = self::preparecompanydata($newel);
                            $res = $company->Update($arCompany['ID'],$arUpdateCompany);  
                            $requisiteFields=[
                                "ENTITY_TYPE_ID"=>4, /*реквизит для компании*/
                                "ENTITY_ID"=>$arCompany['ID'], /* ид нашей созданной компании*/
                                "NAME"=>"Организация",
                                "PRESET_ID"=>1,
                                "RQ_COMPANY_NAME"=>$newel['org'],
                                "RQ_COMPANY_FULL_NAME"=>$newel['org']
                            ];

                            if($newel['inn']) {
                                $requisiteFields['RQ_INN'] = $newel['inn']; 
                            }

                            if($newel['kpp']) {
                                $requisiteFields['RQ_KPP'] = $newel['kpp']; 
                            }
                            
                            $rsRequisite = $requisiteEntity->getList([
                                            "select"=>array("*"),
                                            "filter"=>array(
                                            "ENTITY_ID"=>$arCompany['ID'],
                                            "ENTITY_TYPE_ID"=>\CCrmOwnerType::Company
                                            ),
                                            "order"=>array("SORT"=>"desc","ID"=>"desc")

                            ]);
                            $arRequisiteOld = current($rsRequisite->fetchAll());
                            if($arRequisiteOld) {
                                $requisiteEntity->Update(intval($arRequisiteOld['ID']),$requisiteFields);
                            } else {
                                $resultRequisit = $requisiteEntity->add($requisiteFields);
                            }  
                        }
                    }
                }
            }
        } else {
            exit('Не удалось открыть файл'.$impfile);
        }
        return '\SFZ\Custom\Exchange\ExportImport::parseCompanyXML();';
    }

    private static function preparecompanydata($newel = array()) {
        $arParseCompany = [
            'TITLE' => $newel['org'],
            'OPENED' => 'Y',
            idGalUF => $newel['@attributes']['id'],
            hashUF => $newel['row_hash'],
            "ASSIGNED_BY_ID" => commdir      
        ]; 
        if($newel['tel']) {
            $arParseCompany['FM']['PHONE'] = array(
                'n0' => array(
                    'VALUE_TYPE' => 'WORK',
                    'VALUE' => $newel['tel'],
                )
            );
        }
        if($newel['email']) {
            $arParseCompany['FM']['EMAIL'] = array(
                'n0' => array(
                    'VALUE_TYPE' => 'WORK',
                    'VALUE' => $newel['email'],
                )
            );
        }
        if($newel['name1']) {
            $arParseCompany[marketnameUF] = $newel['name1']; 
        }
        if($newel['name2']) {
            $arParseCompany[marketthroughnameUF] = $newel['name2']; 
        }
        $fieldid = 0;
        if($newel['market']) {
            $fieldid = Utils::getEnumvalue(marketiID, $newel['market']);
            if($fieldid>0) {
                $arParseCompany[marketUF] = $fieldid; 
            }
        }
        $ibid = [];
        if($newel['dealerply']) {
            $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=NAME"=>$newel['dealerply']], ['NAME'=>'desc']));
            if($ibid['ID']>0) {
                $arParseCompany[dealerSyPlyUF] = $ibid['ID']; 
            }
        }
        $ibidsec = [];
        if($newel['daelerlamarty']) {
            $ibidsec = current(Utils::getIBlockElementsByConditions(dealerIB, ["=NAME"=>$newel['daelerlamarty']], ['NAME'=>'desc']));
            if($ibidsec['ID']>0) {
                $arParseCompany[dealerLamUF] = $ibidsec['ID']; 
            }
        }

        if($newel['daelerlamarty2']) {
            $arParseCompany[statusdealUF] = $newel['daelerlamarty2']; 
        }
        
        if($newel['partner']) {
            $arParseCompany[partncodeUF] = $newel['partner']; 
        }
        
        if($newel['mebel']) {
            $arParseCompany[furnitcompUF] = $newel['mebel']; 
        }

        if($newel['name1eng']) {
            $arParseCompany[eng1UF] = $newel['name1eng']; 
        }

        if($newel['name2eng']) {
            $arParseCompany[eng2UF] = $newel['name2eng']; 
        }

        if($newel['issupplier']==0 && $newel['isbuyer']==1) {
            $arParseCompany['COMPANY_TYPE'] = "CUSTOMER";
        } elseif($newel['issupplier']==1 && $newel['isbuyer']==0) {
            $arParseCompany['COMPANY_TYPE'] = "SUPPLIER";
        } else {
            $arParseCompany['COMPANY_TYPE'] = "PARTNER";
        }

        if($newel['managerplyemail']) {
            $user = Utils::getUserbycondition(array('=EMAIL' =>$newel['managerplyemail']));
            if($user['ID']) {
                $arParseCompany[manSyPlyUF] = $user['ID']; 
            }
        }

        if($newel['managerlamartyemail']) {
            $user = Utils::getUserbycondition(array('=EMAIL' =>$newel['managerlamartyemail']));
            if($user['ID']) {
                $arParseCompany[manLamUF] = $user['ID']; 
            }
        }

        if($newel['isarch']) {
            $arParseCompany[archiveUF] = $newel['isarch']; 
        }

        return $arParseCompany; 
    }
}