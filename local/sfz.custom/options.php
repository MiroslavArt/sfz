<?php

$MODULE_ID = 'sfz.custom';

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

global $USER;
if (!$USER->CanDoOperation($MODULE_ID . '_settings')) {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if(!Loader::includeModule('iblock')) {
    ShowError(Loc::GetMessage($MODULE_ID."_MODULE_iblock_NOT_INSTALLED"));
    return;
}

$arIblocks = [];
$dbIblock = \Bitrix\Iblock\IblockTable::query()
    ->setSelect(['ID','NAME'])
    ->exec();
while($arIBlock = $dbIblock->fetch()) {
    $arIblocks[$arIBlock['ID']] = '['.$arIBlock['ID'].']: '.$arIBlock['NAME'];
}

$arAllOptions = [
    'company' => [
        [
            'company_rootXML',
            Loc::getMessage($MODULE_ID.'_rootXML'),
            Option::get($MODULE_ID, '_rootXML'),
            ['text']
        ],
        [
            'company_marketiID',
            Loc::getMessage($MODULE_ID.'_marketiID'),
            Option::get($MODULE_ID, '_marketiID'),
            ['text']
        ],
        [
            'company_marketUF',
            Loc::getMessage($MODULE_ID.'_marketUF'),
            Option::get($MODULE_ID, '_marketUF'),
            ['text']
        ],
        [
            'company_dealerIB',
            Loc::getMessage($MODULE_ID.'_dealerIB'),
            Option::get($MODULE_ID, '_dealerIB'),
            ['text']
        ],
        [
            'company_dealerSyPlyUF',
            Loc::getMessage($MODULE_ID.'_dealerSyPlyUF'),
            Option::get($MODULE_ID, '_dealerSyPlyUF'),
            ['text']
        ],
        [
            'company_dealerLamUF',
            Loc::getMessage($MODULE_ID.'_dealerLamUF'),
            Option::get($MODULE_ID, '_dealerLamUF'),
            ['text']
        ],
        [
            'company_marketnameUF',
            Loc::getMessage($MODULE_ID.'_marketnameUF'),
            Option::get($MODULE_ID, '_marketnameUF'),
            ['text']
        ],
        [
            'company_marketthroughnameUF',
            Loc::getMessage($MODULE_ID.'_marketthroughnameUF'),
            Option::get($MODULE_ID, '_marketthroughnameUF'),
            ['text']
        ],
        [
            'company_idGalUF',
            Loc::getMessage($MODULE_ID.'_idGalUF'),
            Option::get($MODULE_ID, '_idGalUF'),
            ['text']
        ],
        [
            'company_hashUF',
            Loc::getMessage($MODULE_ID.'_hashUF'),
            Option::get($MODULE_ID, '_hashUF'),
            ['text']
        ],
        [
            'company_statusdealUF',
            Loc::getMessage($MODULE_ID.'_statusdealUF'),
            Option::get($MODULE_ID, '_statusdealUF'),
            ['text']
        ],
        [
            'company_manLamUF',
            Loc::getMessage($MODULE_ID.'_manLamUF'),
            Option::get($MODULE_ID, '_manLamUF'),
            ['text']
        ],
        [
            'company_manSyPlyUF',
            Loc::getMessage($MODULE_ID.'_manSyPlyUF'),
            Option::get($MODULE_ID, '_manSyPlyUF'),
            ['text']
        ],
        [
            'company_partncodeUF',
            Loc::getMessage($MODULE_ID.'_partncodeUF'),
            Option::get($MODULE_ID, '_partncodeUF'),
            ['text']
        ],
        [
            'company_furnitcompUF',
            Loc::getMessage($MODULE_ID.'_furnitcompUF'),
            Option::get($MODULE_ID, '_furnitcompUF'),
            ['text']
        ],
];

if(isset($request["save"]) && check_bitrix_sessid()) {
    foreach ($arAllOptions as $part) {
        foreach($part as $arOption) {
            if(is_array($arOption)) {
                __AdmSettingsSaveOption($MODULE_ID, $arOption);
            }
        }
    }
}

$arTabs = [
    [
        "DIV" => "company",
        "TAB" => Loc::getMessage($MODULE_ID.'_company'),
        "ICON" => $MODULE_ID . '_settings',
        "TITLE" => Loc::getMessage($MODULE_ID.'_company'),
        'TYPE' => 'options', //options || rights || user defined
    ],
    /*[
        "DIV" => "1cint",
        "TAB" => Loc::getMessage($MODULE_ID.'_1с'),
        "ICON" => $MODULE_ID . '_settings',
        "TITLE" => Loc::getMessage($MODULE_ID.'_1с'),
        'TYPE' => 'options', //options || rights || user defined
    ],
    [
        "DIV" => "STZint",
        "TAB" => Loc::getMessage($MODULE_ID.'_STZ'),
        "ICON" => $MODULE_ID . '_settings',
        "TITLE" => Loc::getMessage($MODULE_ID.'_STZ'),
        'TYPE' => 'options', //options || rights || user defined
    ]*/
];

$tabControl = new CAdminTabControl("tabControl", $arTabs);

$tabControl->Begin();
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&amp;lang=<?= LANG ?>"
      name="<?= $MODULE_ID ?>_settings">
    <?= bitrix_sessid_post(); ?>
    <?
    foreach ($arTabs as $tab) {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($MODULE_ID, $arAllOptions[$tab['DIV']]);
    }?>
    <?$tabControl->Buttons();?>
    <input type="submit" class="adm-btn-save" name="save" value="<?=Loc::getMessage($MODULE_ID.'_save');?>">
    <?=bitrix_sessid_post();?>
    <? $tabControl->End(); ?>
</form>
