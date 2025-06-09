<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$months_name = [
	'01' => 'января', 
    '02' => 'февраля', 
    '03' => 'марта',
	'04' => 'апреля', 
    '05' => 'мая', 
    '06' => 'июня',
	'07' => 'июля', 
    '08' => 'августа', 
    '09' => 'сентября',
	'10' => 'октября', 
    '11' => 'ноября', 
    '12' => 'декабря'
]; 
?>
    <table class='main-table'>
        <tbody>
            <tr>
                <? if($arResult['TODAY']) {?>
                    <td>
                        <img align="left" alt="С днём рождения!" src="<?=$arResult['CONGRAT']['IMAGE']?>">
                    </td>
                    <td align="center">
                        <span class="HBText">
                            <? $counttoday = count($arResult['TODAY']); ?>
                            Сегодня свой день рождения <? echo $counttoday==1? 'отмечает': 'отмечают'; ?> 
                                <? 
                                
                                $i = 0; 
                                foreach($arResult['TODAY'] as $person) { 
                                    $i++; ?>
                                    <b><?= $person['FIO']?></b> (<?= $person['OTDEL']?>)<? if($i==$counttoday) {
                                        echo ".";
                                    } else { 
                                        echo ",";  
                                    } ?>


                                <? } ?>
                            <br>Поздравляем вас с этим замечательным днём! 
                        </span>
                        <p align="center" class="asterisks">* * *</p>
                        <span class="HBPoem">
                            <? echo nl2br($arResult['CONGRAT']['POEM']); ?>    
                        </span>
                    </td>
                <? } else { ?>
                    <span>Дней рождения сегодня никто не отмечает.</span>
                <? }  ?>    
            </tr>
        </tbody>
    </table>
    <p>
        <span class="NextBDTitle">Ближайшие дни рождения:</span><br>
        <table cols="2">
            <tbody>
                <? foreach($arResult['NEXTDAYS'] as $key=>$value) { 
                    $countfday = count($value);
                    $i = 0; 
                    
                    ?>
                    <tr>
                        <td class="NextDate" width="130" valign="top"><?= 
                            substr($key, 0, 2); 
                        ?> <? echo $months_name[substr($key, 3, 2)]; ?> 
                        <? 
                            $year = date('Y');
                            $month = date('m');
                            if($month=='12' && substr($key, 3, 2)=='01') {
                                $year++; 
                            }
                            echo  $year.' г.';
                        ?> 
                        </td>
                        <td class="NextNames"> — 
                            <? foreach($value as $person) { 
                                $i++;
                                ?>
                                <b><?= $person['FIO']?></b> (<?= $person['OTDEL']?>)<? if($i==$countfday) {
                                        echo ".";
                                    } else { 
                                        echo ",";  
                                    } ?>
                            <? } ?>
                        </td>    
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </p>
