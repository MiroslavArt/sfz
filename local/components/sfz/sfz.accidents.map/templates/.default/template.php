<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

?>
<div id="mapwrapper" class="sfz-map-wrap">
    <table>
        <? for ($i = 1; $i <= $arResult['length']; $i++) { ?>
            <tr>
                <? for ($j = 1; $j <= $arResult['height']; $j++) { ?>
                    <td class="sfz-map-network"></td>
                <? } ?>
            </tr>
        <? } ?>
    </table>
</div>

<script>
    BX.ready(
        $('#mapwrapper').height($('#mapwrapper').width()*0.82);
        $(window).resize(function(){
            $('#mapwrapper').height($('#mapwrapper').width()*0.82);
        });
    );
</script>