<?php

namespace SFZ\Custom\Exchange;

use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Crm\Service;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;

class ExportImport
{
    public function __construct()
    {
        Loader::includeModule('crm');
        $this->result = new Result();
    }

    public function processWebhook()
    {
        try {
            $request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
            $fromnumber = preg_replace("/[^,.0-9]/", '', $request->get('fromnumber'));

            $syplynumbers = explode(",", SYPLYNUM);
            $lamnumbers = explode(",", LAMNUM);

            $tonumber = preg_replace("/[^,.0-9]/", '', $request->get('tonumber'));

            if(in_array($tonumber, $syplynumbers)) {
                $par = 'pl';
            } elseif(in_array($tonumber, $lamnumbers)) {
                $par = 'lm';
            }

            if($par) {
                if ($fromnumber) {
                    $intnumber = self::getIntnumber($fromnumber, $par);
                    if($intnumber) {
                        $this->result->setData([$intnumber]);
                    } else {
                        $this->result->addError(new Error('numbernotfound'));
                    }
                } else {
                    $this->result->addError(new Error('incoming number incorrect'));
                }
            } else {
                $this->result->addError(new Error('own number incorrect'));
            }
        } catch(\Exception $e) {
            $this->result->addError(new Error('internal error'));
        }
        $this->showResponse();
    }

    private function showResponse()
    {
        if($this->result->isSuccess()) {
            $response = ['success' => true, 'intnumber' => current($this->result->getData())];
        } else {
            $response['success'] = false;
            $response['error'] = true;
            $response['message'] = implode(', ',$this->result->getErrorMessages());
        }

        echo Json::encode($response);
    }

    private static function getIntnumber($number, $type = 'pl') {
        $rs = \CCrmFieldMulti::GetList(
            array("ID"=>"ASC"),
            array('TYPE_ID' => 'PHONE')
        );
        $phonescnt = [];
        $phonescmp = [];
        while($ar=$rs->fetch()){
            if(preg_replace("/[^,.0-9]/", '', $ar['VALUE'])==$number) {
                if($ar['ENTITY_ID']=='COMPANY') {
                    $phonescmp[] = $ar;
                } elseif($ar['ENTITY_ID']=='CONTACT') {
                    $phonescnt[] = $ar;
                }
            }
        }
            
        if($phonescnt) {
            $firstcnt = current($phonescnt);
            $cnt = \CCrmContact::GetByID($firstcnt['ELEMENT_ID']);
            if($cnt) {
                $user = Utils::getUserbycondition(array('=ID' =>$cnt['ASSIGNED_BY_ID']));
            } else {
                return false;
            }
        } 
       
        
        if($phonescmp) {
            $firstcmp = current($phonescmp);
            $arFilter = [
                "=ID"=> $firstcmp['ELEMENT_ID'],
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "*",
                marketthroughnameUF
            ];
            $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
            $cmp = $res->fetch(); 
           
            if($cmp[marketthroughnameUF]) {
                $typeval = Utils::getTypevalues(TYPE2ID, $cmp[marketthroughnameUF]);
                if($typeval) {
                    if($type=='pl' && $typeval[TYPE2UFMANSYPLY]) {
                        $user = Utils::getUserbycondition(array('=ID' =>$typeval[TYPE2UFMANSYPLY]));
                    } elseif($type=='lm' && $typeval[TYPE2UFMANLAM]) {
                        $user = Utils::getUserbycondition(array('=ID' =>$typeval[TYPE2UFMANLAM]));
                    } else {
                        $user = Utils::getUserbycondition(array('=ID' =>$cmp['ASSIGNED_BY_ID']));
                    }
                }
            } else {
                $user = Utils::getUserbycondition(array('=ID' =>$cmp['ASSIGNED_BY_ID']));
            }
        } 

        if($user) {
            if($user['UF_PHONE_INNER']) {
                return $user['UF_PHONE_INNER'];
            }
        }
            
        return false; 
    }

    public static function actualiseManagers() {
        // нужен фильтр по дате
        $date = date("Y-m-d");
        
        $selectspmanagerchanges = Utils::getIBlockElementsByConditions(PLYWOODIB, ["ACTIVE"=>'Y', '<=PROPERTY_DATA_SMENY_MENEDZHERA' => $date], 
            ['PROPERTY_SKVOZNAYA_KOMPANIYA_2'=>'DESC', 'PROPERTY_DATA_SMENY_MENEDZHERA'=>'ASC', 'PROPERTY_SOTRUDNIK'=>'ASC']);

        $selectlammanagerchanges = Utils::getIBlockElementsByConditions(LAMARTYIB, ["ACTIVE"=>'Y', '<=PROPERTY_DATA_SMENY_MENEDZHERA' => $date], 
            ['PROPERTY_SKVOZNAYA_KOMPANIYA_2'=>'DESC', 'PROPERTY_DATA_SMENY_MENEDZHERA'=>'ASC', 'PROPERTY_SOTRUDNIK'=>'ASC']);

        $comparr = [];
        $comparrtwo = [];

        foreach($selectspmanagerchanges as $item) {
            if($item['PROPERTY_SKVOZNAYA_KOMPANIYA_2_VALUE']) {
                $comparr[$item['PROPERTY_SKVOZNAYA_KOMPANIYA_2_VALUE']] = $item['PROPERTY_SOTRUDNIK_VALUE'];
            }
        }

        foreach($selectlammanagerchanges as $item) {
            if($item['PROPERTY_SKVOZNAYA_KOMPANIYA_2_VALUE']) {
                $comparrtwo[$item['PROPERTY_SKVOZNAYA_KOMPANIYA_2_VALUE']] = $item['PROPERTY_SOTRUDNIK_VALUE'];
            }
        }

        $factory = Service\Container::getInstance()->getFactory(TYPE2ID);

        $items = $factory->getItems([
            'select' => [],
            'filter' => []
        ]);

        foreach($items as $item) {
            $itemarr = $item->getData();

            $update = false; 
            $updatetwo = false; 

            if(!array_key_exists($item['ID'], $comparr)) {
                if($itemarr[TYPE2UFMANSYPLY]) {
                    $update = true;
                    $man = ''; 
                }
            } else {
                if($itemarr[TYPE2UFMANSYPLY]!=$comparr[$item['ID']]) {
                    $update = true;
                    $man = $comparr[$item['ID']]; 
                }

            }

            if(!array_key_exists($item['ID'], $comparrtwo)) {
                if($itemarr[TYPE2UFMANLAM]) {
                    $updatetwo = true;
                    $mantwo = ''; 
                }
            } else {
                if($itemarr[TYPE2UFMANLAM]!=$comparrtwo[$item['ID']]) {
                    $updatetwo = true;
                    $mantwo = $comparrtwo[$item['ID']]; 
                }
            }

            if($update || $updatetwo) {
                if($update) {
                    $item->set(TYPE2UFMANSYPLY,$man);
                }
                if($updatetwo) {
                    $item->set(TYPE2UFMANLAM,$mantwo);
                }
                $operation = $factory->getUpdateOperation($item);
                $operation
                    ->disableCheckFields()
                    ->disableBizProc()
                    ->disableCheckAccess()
                ;
                $updateResult = $operation->launch();
            }
        }
        return '\SFZ\Custom\Exchange\ExportImport::actualiseManagers();';
    }

    public static function actualiseManagersLeaveLamarty() {
        $impfile = $_SERVER['DOCUMENT_ROOT'].rootXML.'/temp_delegates_lamarty.xml';

        //$attr = $xml->attributes();

        //$attrib = end($attr);

        //echo $attrib['stamp'];

        if (file_exists($impfile)) {

            $xml = simplexml_load_file($impfile);
            foreach($xml as $element) {
                $newel = Utils::xml2array($element);
                $user = Utils::getUserbycondition(array('EMAIL' =>$newel['рабочий_емейл_менеджера']));
                if($user) {
                    $userid = $user['ID'];
                    $factory = Service\Container::getInstance()->getFactory(TYPE2ID);
	
                    $items = $factory->getItems([
                        'select' => [],
                        'filter' => ['TITLE'=>$newel['клиент']]
                    ]);
                    $item = current($items);
                    if($item) {
                        $cdata = $item->getData();
                        if($cdata['ID']) {
 
                            $fromdate =  date("Y-m-d", strtotime($newel['дата_начала_временного_отсутствия'])); 
                            
                            $selectlammanagerchanges1 = Utils::getIBlockElementsByConditions(LAMARTYIB, ["ACTIVE"=>'Y', '=PROPERTY_DATA_SMENY_MENEDZHERA' => $fromdate,
                                '=PROPERTY_SKVOZNAYA_KOMPANIYA_2'=>$cdata['ID'], "=PROPERTY_SOTRUDNIK"=>$userid]);

                            if(!$selectlammanagerchanges1) {
                                    $selectlammanagerchanges2 = Utils::getIBlockElementsByConditions(LAMARTYIB, ["ACTIVE"=>'Y', '<PROPERTY_DATA_SMENY_MENEDZHERA' => $fromdate,
                                    '=PROPERTY_SKVOZNAYA_KOMPANIYA_2'=>$cdata['ID'], "!PROPERTY_ZAMESHCHENIE"=>LAMARTYSUBST], ['PROPERTY_DATA_SMENY_MENEDZHERA'=>'DESC']);

                                    if($selectlammanagerchanges2) {
                                        $curmanager = $selectlammanagerchanges2[0]['PROPERTIES']['SOTRUDNIK']['VALUE'];
                                        $data = [
                                            'ACTIVE' => 'Y',
                                            'NAME' => 'Замещение менеджера',
                                            'PROPERTY_VALUES' => [
                                                'SOTRUDNIK'=> $userid,
                                                'SKVOZNAYA_KOMPANIYA_2' => $cdata['ID'],
                                                'DATA_SMENY_MENEDZHERA' => $newel['дата_начала_временного_отсутствия'],
                                                'ZAMESHCHENIE' => LAMARTYSUBST
                                            ]
                                        ];
                                        $id = Utils::createIBlockElement(LAMARTYIB, $data, []);
                                        
                                        $data = [
                                            'ACTIVE' => 'Y',
                                            'NAME' => 'Восстановление после замещения',
                                            'PROPERTY_VALUES' => [
                                                'SOTRUDNIK'=> $curmanager,
                                                'SKVOZNAYA_KOMPANIYA_2' => $cdata['ID'],
                                                'DATA_SMENY_MENEDZHERA' => $newel['дата_окончания_временного_отсутствия'],
                                                'ZAMESHCHENIE' => LAMARTYSUBSTUNDO
                                            ]
                                        ];
                                        $id = Utils::createIBlockElement(LAMARTYIB, $data, []);
                                    }
                            }
                        }
                    }
                }
            }
        }
        
        return '\SFZ\Custom\Exchange\ExportImport::actualiseManagersLeaveLamarty();';

    }

    public static function dumpCompanyXML()
    {        
        Loader::includeModule('crm');
        $date = date("d.m.Y H:i:s", time() - 24*60*60);
        $fromdate = ConvertDateTime($date); 
        //$selectchanges = Utils::getIBlockElementsByConditions(makeexportIB, [">=TIMESTAMP_X"=>$fromdate]);
        $selectspmanagerchanges = Utils::getIBlockElementsByConditions(PLYWOODIB, ["ACTIVE"=>'Y', 'NAME'=>['Смена менеджера по фанере','Добавление менеджера по продаже фанеры']]);
        $selectlammanagerchanges = Utils::getIBlockElementsByConditions(LAMARTYIB, ["ACTIVE"=>'Y', 'NAME'=>['Смена менеджера по ЛДСП', 'Добавление менеджера по продаже ЛДСП']]);
        //$selectspmanagerchanges = Utils::getIBlockElementsByConditions(PLYWOODIB, [">=TIMESTAMP_X"=>$fromdate]);
        //$selectlammanagerchanges = Utils::getIBlockElementsByConditions(LAMARTYIB, [">=TIMESTAMP_X"=>$fromdate]);
        if(rootXML) {
            if($selectspmanagerchanges || $selectlammanagerchanges) {
            //if($selectchanges || $selectspmanagerchanges || $selectlammanagerchanges) {
                $inputUTF8 = <<<INPUT
                <?xml version="1.0" encoding="UTF-8"?>
                <Catalog>
                    <Changes>
                    </Changes>
                </Catalog>    
                INPUT;

                //$root = simplexml_load_string('<Catalog><Changes></Changes></Catalog>');
                $root = simplexml_load_string($inputUTF8);
                /*foreach($selectchanges as $item) {
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
                }*/
                foreach($selectspmanagerchanges as $item) {
                    $throughcompanytwo = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                    if($throughcompanytwo) {
                        $typeval = Utils::getTypevalues(TYPE2ID, $throughcompanytwo);
                        $change = $root->Changes->addChild('Change');
                        $change->addAttribute('changeid', $item['ID']);
                        $change->field = 'managerplyemail';
                        $change->date = $item['PROPERTIES']['DATA_SMENY_MENEDZHERA']['VALUE'];
                        $change->companyid = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                        $change->company = $typeval['TITLE'];
                        //$change->type = 'Сквозная - 2';
                        $user = Utils::getUserbycondition(array('=ID' =>$item['PROPERTIES']['SOTRUDNIK']['VALUE']));
                        if($user) {
                            $change->newval = $user['EMAIL']; 
                        }
                    }
                }
                foreach($selectlammanagerchanges as $item) {
                    $throughcompanytwo = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                    if($throughcompanytwo) {
                        $typeval = Utils::getTypevalues(TYPE2ID, $throughcompanytwo);
                        $change = $root->Changes->addChild('Change');
                        $change->addAttribute('changeid', $item['ID']);
                        $change->field = 'managerlamartyemail';
                        $change->date = $item['PROPERTIES']['DATA_SMENY_MENEDZHERA']['VALUE'];
                        $change->companyid = $item['PROPERTIES']['SKVOZNAYA_KOMPANIYA_2']['VALUE'];
                        $change->company = $typeval['TITLE'];
                        //$change->type = 'Сквозная - 2';
                        $user = Utils::getUserbycondition(array('=ID' =>$item['PROPERTIES']['SOTRUDNIK']['VALUE']));
                        if($user) {
                            $change->newval = $user['EMAIL']; 
                        }
                    }
                }

            }
            $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("d.m.y").'_'.date("H.i.s").'_'.'managerchangehistory.xml');
            // дамп компаний
            $arFilter = [
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "*",
                "UF_*"
            ];
            $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
            $inputUTF8 = <<<INPUT
            <?xml version="1.0" encoding="UTF-8"?>
            <Catalog>
                <Companies>
                </Companies>
            </Catalog>    
            INPUT;

            //$root = simplexml_load_string('<Catalog><Changes></Changes></Catalog>');
            $root = simplexml_load_string($inputUTF8);
            while($company = $res->Fetch()) {
                $addcompany = $root->Companies->addChild('Company');
                $addcompany->addAttribute('bitrixid', $company['ID']);
                if($company[idGalUF]) {
                    $addcompany->addAttribute('galaxyid', $company[idGalUF]);
                }

                if($company['HAS_EMAIL']=='Y') {
                    $email = '';
                    $rs = \CCrmFieldMulti::GetList(
                        array("ID"=>"ASC"),
                        array('ENTITY_ID'=>'COMPANY', 'TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $company['ID'])
                    );
                    while($ar=$rs->fetch()){
                        if($email) {
                            $email = $email.','.$ar['VALUE'];  
                        } else {
                            $email = $ar['VALUE']; 
                        }
                    }
                    
                    if($email) {
                        $addcompany->email = $email;
                    }
                }
                if($company['HAS_PHONE']=='Y') {
                    $phone = '';
                    $rs = \CCrmFieldMulti::GetList(
                        array("ID"=>"ASC"),
                        array('ENTITY_ID'=>'COMPANY', 'TYPE_ID' => 'PHONE', 'ELEMENT_ID' => $company['ID'])
                    );
                    while($ar=$rs->fetch()){
                        if($phone) {
                            $phone = $phone.','.$ar['VALUE'];  
                        } else {
                            $phone = $ar['VALUE']; 
                        } 
                    }              
                    if($phone) {
                        $addcompany->tel = $phone;
                    }
                }
                if($company[marketnameUF]) {
                    $typeval = Utils::getTypevalues(TYPE1ID, $company[marketnameUF]);
                    if($typeval) {
                        $thr1company = $addcompany->addChild('name1');
                        $thr1company->addAttribute('id', $typeval['ID']);
                        $thr1company->rustitle = $typeval['TITLE']; 
                        if($typeval[TYPE1UFENG]) {
                            $thr1company->entitle = $typeval[TYPE1UFENG];
                        }
                    }
                }
                if($company[marketthroughnameUF]) {
                    $typeval = Utils::getTypevalues(TYPE2ID, $company[marketthroughnameUF]);
                    if($typeval) {
                        $thr1company = $addcompany->addChild('name2');
                        $thr1company->addAttribute('id', $typeval['ID']);
                        $thr1company->rustitle = $typeval['TITLE']; 
                        if($typeval[TYPE2UFENG]) {
                            $thr1company->entitle = $typeval[TYPE2UFENG];
                        }
                    }
                }
                
                if($company[dealerSyPlyUF]) {
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$company[dealerSyPlyUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $addcompany->dealerply = $ibid['NAME'];
                    }
                }
                if($company[dealerLamUF]) {
                    $ibid = current(Utils::getIBlockElementsByConditions(dealerIB, ["=ID"=>$company[dealerLamUF]], ['NAME'=>'desc']));
                    if($ibid) {
                        $addcompany->dealerlamarty = $ibid['NAME'];
                    }
                }
                if($company[marketUF]) {
                    $fieldval = Utils::getEnumvalue(marketiID, $company[marketUF], 'value');
                    if($fieldval) {
                        $addcompany->market = $fieldval;
                    }
                }
                if($company[statusdealUF]) {
                    $const = STATUSDEALUFID; 
                    if(!$const) {
                        $const = 302;
                    }
                    $fieldvalstat = Utils::getEnumvalue($const, $company[statusdealUF], 'value');
                    $addcompany->dealerlamarty2 = $fieldvalstat;
                    //$addcompany->dealerlamarty2 = $company[statusdealUF];
                }
                
                if($company[partncodeUF]) {
                    $addcompany->partner = $company[partncodeUF];
                }
                if($company[furnitcompUF]) {
                    $addcompany->mebel = $company[furnitcompUF];
                }
                
                if($company[marketinUF]) {
                    $addcompany->ismarket = $company[marketinUF]; 
                } else {
                    $addcompany->ismarket = 0; 
                }

                if($company[archiveUF]) {
                    $addcompany->isarch = $company[archiveUF]; 
                } else {
                    $addcompany->isarch = 0; 
                }
            }
            $root->asXML($_SERVER['DOCUMENT_ROOT'].rootXML.'/'.date("d.m.y").'_'.date("H.i.s").'_'.'actualcompanylist.xml');
        }
        return '\SFZ\Custom\Exchange\ExportImport::dumpCompanyXML();';
    }
    
    public static function parseCompanyXML()
    {        
        Loader::includeModule('crm');
        $impfile = $_SERVER['DOCUMENT_ROOT'].rootXML.'/'.importfileXML;
        if (file_exists($impfile)) {
            $arFilter = [
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "ID",
                idGalUF,
                hashUF	
            ];
            $res = \CCrmCompany::GetListEx(Array(), $arFilter, false, false, $arSelect);
            
            $hash = [];
            
            while($item = $res->Fetch()) {
                if($item[idGalUF]) {
                    $hash[$item[idGalUF]]['ID'] = $item['ID'];
                    $hash[$item[idGalUF]]['hash'] = $item[hashUF];
                }
            }

            $xml = simplexml_load_file($impfile);
            $contr = $xml->Contragent;
            $test = 0; 
            $company=new \CCrmCompany(false);
            $requisiteEntity = new \Bitrix\Crm\EntityRequisite();
            foreach($contr as $element) {
                $newel = Utils::xml2array($element);

                $idgal = $newel['@attributes']['id'];
                $title = $newel['org'];

                if($idgal && $title) {

                    if(!$hash[$idgal]) {

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
                        if($hash[$idgal]['hash']!=$newel['row_hash']) {
                            echo '<pre>';
                            print_r($newel['row_hash']);
                            echo '</pre>';
                            $arUpdateCompany = self::preparecompanydata($newel, $hash[$idgal]['ID']);
                            $res = $company->Update($hash[$idgal]['ID'],$arUpdateCompany);  
                            $requisiteFields=[
                                "ENTITY_TYPE_ID"=>4, /*реквизит для компании*/
                                "ENTITY_ID"=>$hash[$idgal]['ID'], /* ид нашей созданной компании*/
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
                                            "ENTITY_ID"=>$hash[$idgal]['ID'],
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
            /*marketnameUF => "",
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
            marketinUF => ""*/
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

            $item = current($items);
            if($item) {
                $cdata = $item->getData();
                $arParseCompany[marketnameUF] = $cdata['ID']; 
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
            $item = current($items);
            if($item) {
                $cdata = $item->getData();
                $arParseCompany[marketthroughnameUF] = $cdata['ID']; 
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
            $cleanmarket = preg_replace('/\s+/', '', $newel['market']);
            $fieldid = Utils::getEnumvalue(marketiID, $cleanmarket);
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
        if($newel['dealerlamarty']) {
            $ibidsec = current(Utils::getIBlockElementsByConditions(dealerIB, ["=NAME"=>$newel['dealerlamarty']], ['NAME'=>'desc']));
            if($ibidsec['ID']>0) {
                $arParseCompany[dealerLamUF] = $ibidsec['ID']; 
            }
        }

        if($newel['dealerlamarty2']) {
            $arParseCompany[statusdealUF] = $newel['dealerlamarty2']; 
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

    /*public static function leadupdate() {
        if (\Bitrix\Main\Loader::includeModule('bizproc')) {

            $arErrorsTmp = [];
            $wfId = \CBPDocument::StartWorkflow(
                WFID,
                array("crm", "CCrmDocumentLead", 'LEAD_' . 4),
                [
                    \CBPDocument::PARAM_TAGRET_USER => "user_1",
                    \CBPDocument::PARAM_DOCUMENT_EVENT_TYPE => \CBPDocumentEventType::Manual,
                    'companyadded' => false
                ],
                $arErrorsTmp
            );
            if (!empty($arErrorsTmp)) {
                echo "started";
            }
        }
       
        \Bitrix\Main\Loader::includeModule('crm');

        $arFilter = [
            "=COMPANY_ID" => 0,
            "STATUS_ID" => LEADSTAGE,
            "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
        ];
        $arSelect = [
            "*",
            "UF_*"
        ];
        $res = \CCrmLead::GetListEx(Array(), $arFilter, false, false, $arSelect);

        while($lead = $res->Fetch()) {
            echo "<pre>";
            print_r($lead);
            echo "</pre>";

        }
     }*/
    public static function syncFeeds() {
        if(Loader::includeModule('crm')) {
            $arFilter = [
                "CHECK_PERMISSIONS"=>"N" //не проверять права доступа текущего пользователя
            ];
            $arSelect = [
                "ID", COMPANYUF1, COMPANYUF2
            ];
            $res = \CCrmCompany::GetListEx([], $arFilter, false, false, $arSelect);
        
            while($arEnum = $res->Fetch()) {
                $contactIDs = \Bitrix\Crm\Binding\ContactCompanyTable::getCompanyContactIDs($arEnum['ID']);
                if($contactIDs) {
                   foreach($contactIDs as $item) {
                        \Bitrix\Crm\Timeline\Entity\TimelineBindingTable::attach(
                            \CCrmOwnerType::Contact,
                            $item, 
                            \CCrmOwnerType::Company,
                            $arEnum['ID'],
                            [
                                \Bitrix\Crm\Timeline\TimelineType::ACTIVITY
                            ]
                        );
                        \CCrmActivity::AttachBinding(\CCrmOwnerType::Contact, $item, \CCrmOwnerType::Company, $arEnum['ID']);
                        if($arEnum[COMPANYUF1]) {
                            \Bitrix\Crm\Timeline\Entity\TimelineBindingTable::attach(
                                \CCrmOwnerType::Contact,
                                $item, 
                                TYPE1ID,
                                $arEnum[COMPANYUF1],
                                [
                                    \Bitrix\Crm\Timeline\TimelineType::ACTIVITY
                                ]
                            );
                            \CCrmActivity::AttachBinding(\CCrmOwnerType::Contact, $item, TYPE1ID, $arEnum[COMPANYUF1]);
        
                        }
                        if($arEnum[COMPANYUF2]) {
                            \Bitrix\Crm\Timeline\Entity\TimelineBindingTable::attach(
                                \CCrmOwnerType::Contact,
                                $item, 
                                TYPE2ID,
                                $arEnum[COMPANYUF2],
                                [
                                    \Bitrix\Crm\Timeline\TimelineType::ACTIVITY
                                ]
                            );
                            \CCrmActivity::AttachBinding(\CCrmOwnerType::Contact, $item, TYPE2ID, $arEnum[COMPANYUF2]);
        
                        }
        
                    }
        
                } 
            }
        }
        return '\SFZ\Custom\Exchange\ExportImport::syncFeeds();';
    }

    public static function preparebirthdayXML()
    {
        return '\SFZ\Custom\Exchange\ExportImport::preparebirthdayXML();';
    }

    public static function preparebirthdayoldXML()
    {
        $impfile = $_SERVER['DOCUMENT_ROOT'].rootXML.'/b24_persons.xml';

        $imptab = [];
        $implel = [];

        if (file_exists($impfile)) {

            $xml = simplexml_load_file($impfile);
            foreach($xml as $element) {
                $newel = Utils::xml2array($element);
                $implel[] = $newel['tab'];
                $imptab[$newel['tab']] = $newel;
        
            }
        }

        $selectempl = Utils::getIBlockElementsByConditions(IBBIRTHD, ["ACTIVE"=>'Y', '=NAME' => $implel]);
    
        $curel = [];

        foreach($selectempl as $value) {
            $curel[] = $value['NAME']; 
        }

        $diff = array_diff($implel, $curel);

        foreach($diff as $value) {
            $data = [
                'ACTIVE' => 'Y',
                'NAME' => $value,
                'PROPERTY_VALUES' => [
                    'FIO'=> $imptab[$value]['fio'],
                    'OTDEL' => mb_strtolower($imptab[$value]['dept']),
                    'DEN_ROZHDENIYA' => $imptab[$value]['bdt']
                ]
            ];
            $id = Utils::createIBlockElement(IBBIRTHD, $data, []);
        }

        $selectempl = Utils::getIBlockElementsByConditions(IBBIRTHD, ["ACTIVE"=>'Y', '!=NAME' => $implel]);

        foreach($selectempl as $value) {
            \CIBlockElement::Delete($value['ID']);

        }
        return '\SFZ\Custom\Exchange\ExportImport::preparebirthdayXML();';
    }
}