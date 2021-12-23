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
            $test = 0; 
            foreach($contr as $element) {
                if($test<5) {
                    $newel = Utils::xml2array($element);
                    echo "<pre>";
                    print_r($newel);
                    echo "</pre>";
                    $idgal = $newel['@attributes']['id'];
                    $title = $newel['org'];
                    if($idga && $title) {
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
                        $company=new \CCrmCompany();
                        if(empty($arCompany)) {
                            $arNewCompany = [
                                'TITLE' => $title,
                                'OPENED' => 'Y',
                                idGalUF => $idga,
                                hashUF => $newel['row_hash'],
                                "ASSIGNED_BY_ID" => commdir      
                            ]; 
                            if($newel['tel']) {
                                $arNewCompany['FM']['PHONE'] = array(
                                    'n0' => array(
                                        'VALUE_TYPE' => 'WORK',
                                        'VALUE' => $newel['tel'],
                                    )
                                );
                            }
                            if($newel['email']) {
                                $arNewCompany['FM']['EMAIL'] = array(
                                    'n0' => array(
                                        'VALUE_TYPE' => 'WORK',
                                        'VALUE' => $newel['email'],
                                    )
                                );
                            }
                            if($newel['name1']) {
                                $arNewCompany[marketnameUF] = $newel['name1']; 
                            }
                            if($newel['name2']) {
                                $arNewCompany[marketthroughnameUF] = $newel['name2']; 
                            }
                            $fieldid = 0;
                            if($newel['market']) {
                                $fieldid = Utils::getEnumvalue(marketiID, $newel['market']);
                                if($fieldid>0) {
                                    $arNewCompany[marketUF] = $fieldid; 
                                }
                            }
                            $ibid = [];
                            if($newel['dealerply']) {
                                $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=NAME"=>$newel['dealerply']], ['NAME'=>'desc']));
                                if($ibid['ID']>0) {
                                    $arNewCompany[dealerSyPlyUF] = $ibid['ID']; 
                                }
                            }
                            $ibidsec = [];
                            if($newel['daelerlamarty']) {
                                $ibidsec = current(Utils::getIBlockElementsByConditions(dealerIB, ["=NAME"=>$newel['daelerlamarty']], ['NAME'=>'desc']));
                                if($ibidsec['ID']>0) {
                                    $arNewCompany[dealerLamUF] = $ibidsec['ID']; 
                                }
                            }

                            if($newel['daelerlamarty2']) {
                                $arNewCompany[statusdealUF] = $newel['daelerlamarty2']; 
                            }
                            
                            if($newel['partner']) {
                                $arNewCompany[partncodeUF] = $newel['partner']; 
                            }
                            
                            if($newel['mebel']) {
                                $arNewCompany[furnitcompUF] = $newel['mebel']; 
                            }

                            if($newel['name1eng']) {
                                $arNewCompany[eng1UF] = $newel['name1eng']; 
                            }

                            if($newel['name2eng']) {
                                $arNewCompany[eng2UF] = $newel['name2eng']; 
                            }

                            if($newel['issupplier']==0 && $newel['isbuyer']==1) {
                                $arNewCompany['COMPANY_TYPE'] == "CUSTOMER";
                            } elseif($newel['issupplier']==1 && $newel['isbuyer']==0) {
                                $arNewCompany['COMPANY_TYPE'] == "SUPPLIER";
                            } else {
                                $arNewCompany['COMPANY_TYPE'] == "PARTNER";
                            }

                            if($newel['managerplyemail']) {
                                $user = Utils::getUserbycondition(array('=EMAIL' =>$newel['managerplyemail']));
                                if($user['ID']) {
                                    $arNewCompany[manSyPlyUF] = $user['ID']; 
                                }
                            }

                            if($newel['managerlamartyemail']) {
                                $user = Utils::getUserbycondition(array('=EMAIL' =>$newel['managerlamartyemail']));
                                if($user['ID']) {
                                    $arNewCompany[manLamUF] = $user['ID']; 
                                }
                            }
                            
                            $company = new \CCrmCompany(false);
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

                                $requisiteEntity = new Bitrix\Crm\EntityRequisite();
                                $resultRequisit = $requisiteEntity->add($requisiteFields);
                            }
                        } else {
    
                        }
                    }
                }    
            }
        } else {
            exit('Не удалось открыть файл'.rootXML);
        }
        return '\SFZ\Custom\Exchange\ExportImport::parseCompanyXML();';
    }
}