<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//echo "<pre>";
//print_r($arResult);
//echo "</pre>";
?>
<div class="sfz-map-wrap">
    <table>
        <? for ($i = 1; $i <= $arResult['length']; $i++) { ?>
            <tr>
                <? for ($i = 1; $i <= $arResult['height']; $i++) { ?>
                    <td class="sfz-map-network"></td>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>