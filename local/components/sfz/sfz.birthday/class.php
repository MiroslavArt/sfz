<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use SFZ\Custom\Helpers\Utils;
use \Bitrix\Main\Loader;

class Birthday extends CBitrixComponent
{
    
    public function executeComponent()
    {
        global $APPLICATION;

        if ($this->startResultCache())
		{

            $arResult =& $this->arResult;
            
            $dates = []; 

            $fromdate = date("d.m");

            $arResult['PERIOD'] = $this->arParams['PERIOD'];

            for ($i = 1; $i <= $arResult['PERIOD']; $i++) {
                if($i == 1) {
                    $andata = date("d.m", strtotime( '+'.$i.'day')); 
                } else {
                    $andata = date("d.m", strtotime( '+'.$i.'days')); 
                }

                $dates[] = $andata; 
            }


            $selectbirthday = Utils::getIBlockElementsByConditions(IBBIRTHD, ["ACTIVE"=>'Y']);

            $birthdayarr = [];
            foreach($dates as $val) {
                $birthdayarr['nextdays'][$val] = []; 

            }

            foreach($selectbirthday as $value) {
                $element = [
                    'FIO' => $value['PROPERTIES']['FIO']['VALUE'],
                    'OTDEL' => $value['PROPERTIES']['OTDEL']['VALUE'],
                    'DEN_ROZHDENIYA' => $value['PROPERTIES']['DEN_ROZHDENIYA']['VALUE']	
                ];

                $birthday = date("d.m", strtotime($element['DEN_ROZHDENIYA']));

                if($birthday==$fromdate) {
                    $birthdayarr['today'][] = $element; 
                } elseif(in_array($birthday, $dates)) {
                    $birthdayarr['nextdays'][$birthday][] = $element; 
                }
            }

            foreach($birthdayarr['nextdays'] as $key => $val) {
                if(!$val) {
                    unset($birthdayarr['nextdays'][$key]);
                }

            }
            $arResult['TODAY'] = $birthdayarr['today'];
            $arResult['NEXTDAYS'] = $birthdayarr['nextdays'];
            
            $congrats = Utils::getIBlockElementsByConditions(IBCONGBIRTHD, ["ACTIVE"=>'Y']);

            $congrat = array_rand($congrats, 1);

            $congratresult['POEM'] = $congrats[$congrat]['PROPERTIES']['STIKHOTVORENIE']['VALUE']; 
            $congratresult['IMAGE'] = CFile::GetPath($congrats[$congrat]['PROPERTIES']['KARTINKA']['VALUE']); 

            $arResult['CONGRAT'] = $congratresult;

            $this->includeComponentTemplate();

        } 
    }
}