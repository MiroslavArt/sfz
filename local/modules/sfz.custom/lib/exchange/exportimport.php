<?php

namespace SFZ\Custom\Exchange;

use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Crm\Service;

class ExportImport
{
    public function __construct()
    {
        Loader::includeModule('crm');
    }

    public static function dumpCompanyXML()
    {        
        Loader::includeModule('crm');
        $date = date("d.m.Y H:i:s", time() - 24*60*60);
        $fromdate = ConvertDateTime($date); 
        $selectchanges = Utils::getIBlockElementsByConditions(makeexportIB, [">=TIMESTAMP_X"=>$fromdate]);
        $selectspmanagerchanges = Utils::getIBlockElementsByConditions(PLYWOODIB, [">=TIMESTAMP_X"=>$fromdate]);
        $selectlammanagerchanges = Utils::getIBlockElementsByConditions(LAMARTYIB, [">=TIMESTAMP_X"=>$fromdate]);
        if(rootXML) {
            if($selectchanges || $selectspmanagerchanges || $selectlammanagerchanges) {
                $root = simplexml_load_string('<Catalog><Changes></Changes></Catalog>');
                foreach($selectchanges as $item) {
                    $company = $item['PROPERTIES']['KOMPANIYA']['VALUE'];
                    $throughcompanyone = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_1']['VALUE'];
                    $throughcompanytwo = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                    if($company && !$throughcompanyone && !$throughcompanytwo) {
                        $arFilter = [
                            "ID" => $company, //выбираем определенную сделку по ID
                            "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
                        ];
                        $arSelect = [
                            "ID",
                            idGalUF
                        ];
                        $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
                        $arCompany = $res->fetch();
                        if($arCompany[idGalUF]) {
                            $change = $root->Changes->addChild('Change');
                            $change->addAttribute('changeid', $item['ID']);
                            $change->field = $item['NAME'];
                            $change->date = $item['PROPERTIES']['DATA_IZMENENIYA']['VALUE'];
                            $change->company = $arCompany[idGalUF];
                            $change->type = 'компания';
                            $change->newval = $item['PROPERTIES']['NOVOE_ZNACHENIE']['VALUE'];
                        }
                    } elseif(!$company && $throughcompanyone && !$throughcompanytwo) {
                        $typeval = Utils::getTypevalues(TYPE1ID, $throughcompanyone);
                        if($typeval) {
                            $change = $root->Changes->addChild('Change');
                            $change->addAttribute('changeid', $item['ID']);
                            $change->field = $item['NAME'];
                            $change->date = $item['PROPERTIES']['DATA_IZMENENIYA']['VALUE'];
                            $change->company = $typeval['TITLE'];
                            $change->type = 'Сквозная - 1';
                            $change->newval = $item['PROPERTIES']['NOVOE_ZNACHENIE']['VALUE']; 
                        }
                    } elseif(!$company && !$throughcompanyone && $throughcompanytwo) {
                        $typeval = Utils::getTypevalues(TYPE2ID, $throughcompanytwo);
                        if($typeval) {
                            $change = $root->Changes->addChild('Change');
                            $change->addAttribute('changeid', $item['ID']);
                            $change->field = $item['NAME'];
                            $change->date = $item['PROPERTIES']['DATA_IZMENENIYA']['VALUE'];
                            $change->company = $typeval['TITLE'];
                            $change->type = 'Сквозная - 2';
                            $change->newval = $item['PROPERTIES']['NOVOE_ZNACHENIE']['VALUE']; 
                        }
                    }
                }
                foreach($selectspmanagerchanges as $item) {
                    $throughcompanytwo = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                    $typeval = Utils::getTypevalues(TYPE2ID, $throughcompanytwo);
                    if($throughcompanytwo) {
                        $change = $root->Changes->addChild('Change');
                        $change->addAttribute('changeid', $item['ID']);
                        $change->field = 'managerplyemail';
                        $change->date = $item['PROPERTIES']['DATA_SMENY_MENEDZHERA']['VALUE'];
                        $change->company = $typeval['TITLE'];
                        $change->type = 'Сквозная - 2';
                        $user = Utils::getUserbycondition(array('=ID' =>$item['PROPERTIES']['SOTRUDNIK']['VALUE']));
                        if($user) {
                            $change->newval = $user['EMAIL']; 
                        }
                    }
                }
                foreach($selectlammanagerchanges as $item) {
                    $throughcompanytwo = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                    $typeval = Utils::getTypevalues(TYPE2ID, $throughcompanytwo);
                    if($throughcompanytwo) {
                        $change = $root->Changes->addChild('Change');
                        $change->addAttribute('changeid', $item['ID']);
                        $change->field = 'managerlamartyemail';
                        $change->date = $item['PROPERTIES']['DATA_SMENY_MENEDZHERA']['VALUE'];
                        $change->company = $typeval['TITLE'];
                        $change->type = 'Сквозная - 2';
                        $user = Utils::getUserbycondition(array('=ID' =>$item['PROPERTIES']['SOTRUDNIK']['VALUE']));
                        if($user) {
                            $change->newval = $user['EMAIL']; 
                        }
                    }
                }

            }
            
            
            $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("d.m.y").'_'.date("H.i.s").'_'.'companyupdate.xml');
        }
        return '\SFZ\Custom\Exchange\ExportImport::dumpCompanyXML();';
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
                            $arUpdateCompany = self::preparecompanydata($newel, $arCompany['ID']);
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

    private static function preparecompanydata($newel = array(), $companyid = NULL) {
        $arParseCompany = [
            'TITLE' => $newel['org'],
            'OPENED' => 'Y',
            idGalUF => $newel['@attributes']['id'],
            hashUF => $newel['row_hash'],
            "ASSIGNED_BY_ID" => commdir,
            marketnameUF => "",
            marketthroughnameUF => "",
            dealerSyPlyUF => "",
            dealerLamUF => "",
            statusdealUF => "",
            partncodeUF => "",
            furnitcompUF => "",
            eng1UF => "",
            eng2UF => "",
            manSyPlyUF => "",
            manLamUF => "",
            marketUF => "",
            archiveUF => "",
            marketinUF => ""
        ]; 
        
        if($newel['tel']) {
            if(self::checkcompanycontact('PHONE', $companyid, $newel['tel'])) {
                $arParseCompany['FM']['PHONE'] = array(
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $newel['tel'],
                    )
                );
            }
        }
        if($newel['email']) {
            if(self::checkcompanycontact('EMAIL', $companyid, $newel['email'])) {
                $arParseCompany['FM']['EMAIL'] = array(
                    'n0' => array(
                        'VALUE_TYPE' => 'WORK',
                        'VALUE' => $newel['email'],
                    )
                );
            }
        }
        if($newel['name1']) {
            $factory = Service\Container::getInstance()->getFactory(TYPE1ID);  
            $items = $factory->getItems([
                'select' => [],
                'filter' => ['TITLE'=>$newel['name1']]
            ]);
            if($items) {
                $arParseCompany[marketnameUF] = $items[0]['ID']; 
            } else {
                $item = $factory->createItem(['TITLE'=>$newel['name1'], 'ASSIGNED_BY_ID'=>commdir]);
                $operation = $factory->getAddOperation($item);
                $operation
                    ->disableCheckFields()
                    ->disableBizProc()
                    ->disableCheckAccess()
                ;
                $addResult = $operation->launch();
                $errorMessages = $addResult->getErrorMessages();
                if ($addResult->isSuccess())
                {
                    $newId = $item->getId();
                    if($newId) {
                        $arParseCompany[marketnameUF] = $newId; 
                    }
                }
            }
        }
        if($newel['name2']) {
            $factory = Service\Container::getInstance()->getFactory(TYPE2ID); 
            $items = $factory->getItems([
                'select' => [],
                'filter' => ['TITLE'=>$newel['name2']]
            ]);
            if($items) {
                $arParseCompany[marketnameUF] = $items[0]['ID']; 
            } else {
                $item = $factory->createItem(['TITLE'=>$newel['name2'], 'ASSIGNED_BY_ID'=>commdir]);
                $operation = $factory->getAddOperation($item);
                $operation
                    ->disableCheckFields()
                    ->disableBizProc()
                    ->disableCheckAccess()
                ;
                $addResult = $operation->launch();
                $errorMessages = $addResult->getErrorMessages();
                if ($addResult->isSuccess())
                {
                    $newId = $item->getId();
                    if($newId) {
                        $arParseCompany[marketthroughnameUF] = $newId; 
                    }
                }
            }
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

        if($newel['ismarket']) {
            $arParseCompany[marketinUF] = $newel['ismarket']; 
        } 
        return $arParseCompany; 
    }


    private static function checkcompanycontact($contacttype, $companyid, $checkvalue) {
        if(!$companyid) {
            return true;
        } else {
            $rs = \CCrmFieldMulti::GetList(
                array("ID"=>"ASC"),
                array('ENTITY_ID'=>'COMPANY', 'TYPE_ID' => $contacttype, 'ELEMENT_ID' => $companyid)
            );
            while($ar=$rs->fetch()){
                if($ar['VALUE']==$checkvalue) {
                    return false;
                }    
            }
            return true; 
        }
    }
}