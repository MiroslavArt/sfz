<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
?>
<div>
    <input type="checkbox" id="grid" name="grid">
    <label class="sfz-map-title" for="grid"><? echo GetMessage("SHOW_GRID"); ?></label>
</div>
<!--<div id='controls' class='hidden'>
      <a href='#' id='rotate_left'  title='Rotate left'><i class='fa fa-rotate-left'></i></a>
      <a href='#' id='zoom_out'     title='Zoom out'><i class='fa fa-search-minus'></i></a>
      <a href='#' id='fit'          title='Fit image'><i class='fa fa-arrows-alt'></i></a>
      <a href='#' id='zoom_in'      title='Zoom in'><i class='fa fa-search-plus'></i></a>
      <a href='#' id='rotate_right' title='Rotate right'><i class='fa fa-rotate-right'></i></a>
    </div>-->
<div id="mapwrapper" class="sfz-map-wrap">
    <table class="sfz-map-table">
        <? for ($i = 1; $i <= $arResult['length']; $i++) { ?>
            <tr>
                <? for ($j = 1; $j <= $arResult['height']; $j++) { ?>
                    <td class="sfz-map-background">
                        <? if($arResult['accidents']['x'.$j.'y'.$i]) { ?>
                            <div class="sfz-map-cell">
                                <? foreach($arResult['accidents']['x'.$j.'y'.$i] as $key => $item) { ?>
                                    <span class="sfz-map-cell-content" data-id="<?= $key ?>" data-year="<?= $item['YEAR'] ?>"
                                        data-tyazh="<?= $item['HARDNESS'] ?>" data-dolzhn="<?= $item['POSITION'] ?>"
                                            data-type="<?= $item['TYPE'] ?>"><?= $item['DESCR'] ?></span>
                                <? } ?>
                            </div>
                        <? } ?>
                    </td>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>

