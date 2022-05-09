<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

?>
<div id="mapwrapper" class="sfz-map-wrap">
    <table class="sfz-map-table">
        <? for ($i = 1; $i <= $arResult['length']; $i++) { ?>
            <tr>
                <? for ($j = 1; $j <= $arResult['height']; $j++) { ?>
                    <td class="sfz-map-network">
                        <? if($arResult['accidents']['x'.$j.'y'.$i]) { ?>
                            <div class="sfz-map-cell"></div>
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
    });
</script>