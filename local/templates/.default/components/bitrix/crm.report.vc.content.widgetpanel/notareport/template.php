<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Context;
use Bitrix\Main\UI\Filter;
use Bitrix\Crm\Integration\Report\Filter\Deal\SalesDynamicFilter;
use Bitrix\Crm\Integration\Report\Dashboard\Sales\SalesDynamic;

\Bitrix\Main\UI\Extension::load('ui.fonts.opensans');

CJSCore::RegisterExt('crm_common', [
	'js' => '/bitrix/js/crm/common.js',
	'css' => '/bitrix/js/crm/css/crm.css',
]);

CJSCore::Init(['crm_common']);
\Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/components/bitrix/crm.report.vc.content.widgetpanel/templates/.default/style.css');

$guid = $arResult['WIDGET_PANEL_PARAMS']["GUID"];


if($guid)
{

	//$filterkey = 'report_board_'. SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ" .'_filter';
	//$arResult['WIDGET_PANEL_PARAMS']['FILTERKEY'] = $filterkey;
}
?>
<div id="report-widget-panel-container">
<?
	
	$APPLICATION->IncludeComponent(
		'sfz:sfz.widget.panel',
		'dealpipeline',
		$arResult['WIDGET_PANEL_PARAMS']
	   );

?>
</div>
