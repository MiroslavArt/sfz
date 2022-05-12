<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
?>
<div>
    <input type="checkbox" id="grid" name="grid">
    <label class="sfz-map-title" for="grid"><? echo GetMessage("SHOW_GRID"); ?></label>
</div>
<div id="mapwrapper" class="sfz-map-wrap">
    <table class="sfz-map-table">
        <? for ($i = 1; $i <= $arResult['length']; $i++) { ?>
            <tr>
                <? for ($j = 1; $j <= $arResult['height']; $j++) { ?>
                    <td>
                        <? if($arResult['accidents']['x'.$j.'y'.$i]) { ?>
                            <div class="sfz-map-cell">
                                <? foreach($arResult['accidents']['x'.$j.'y'.$i] as $key => $item) { ?>
                                    <span class="sfz-map-cell-content" data-id="<?= $key ?>" data-year="<?= $item['YEAR'] ?>"
                                        data-tyazh="<?= $item['HARDNESS'] ?>" data-dolzhn="<?= $item['POSITION'] ?>"><?= $item['DESCR'] ?></span>
                                <? } ?>
                            </div>
                        <? } ?>
                    </td>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>

<script>
    BX.ready(function() {
        //var x = 1111;
        //console.log(x)
        $('#mapwrapper').height($('#mapwrapper').width()*0.89);
        $(window).resize(function(){
            $('#mapwrapper').height($('#mapwrapper').width()*0.89);
        }); 
        $( ".sfz-map-cell" ).mouseover(function(e) {
            Appear(e.currentTarget)
        });
        $( ".sfz-map-cell" ).mouseout(function(e) {
            Leave(e.currentTarget)
        });
        $('#grid').click(function(){
            if ($(this).is(':checked')){
                $('td').addClass("sfz-map-network")
            } else {
                $('td').removeClass("sfz-map-network")
            }
        });   
    });
</script>