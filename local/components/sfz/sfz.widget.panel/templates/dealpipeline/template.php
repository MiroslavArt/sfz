<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
//echo "<pre>";
//print_r($arParams);
//echo "</pre>";

//echo "<pre>";
//print_r($arResult['PIPELINE_DATA']);
//echo "</pre>";
//echo "<pre>";
//print_r($arResult['FILTER_FIELDS']);
//echo "</pre>";
CJSCore::Init([
	'amcharts',
	'amcharts_funnel',
	'amcharts_serial',
	'amcharts_pie',
]);
\Bitrix\Main\UI\Extension::load("ui.bootstrap4");
$compname = $this->getComponent()->getName();
?>
<div class="row chartrow justify-content-md-center">
    <div class="col-6" id="pipeline">
    </div>
</div>

<script type="text/javascript">
    BX.ready(
		function()
		{
            BX.sfz.crm.pipeline.init(<?= CUtil::PhpToJSObject($arResult['PIPELINE_DATA'])?>, <?= CUtil::PhpToJSObject($compname)?>); 
        }
	);
</script>
