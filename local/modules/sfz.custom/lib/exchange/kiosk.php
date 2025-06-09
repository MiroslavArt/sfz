<?php

namespace SFZ\Custom\Exchange;

class Kiosk {
	
	public static function resetLeftMenuSequence() {
		$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
		$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

		define("NO_KEEP_STATISTIC", true);
		define("NOT_CHECK_PERMISSIONS",true);
		define("BX_CRONTAB", true);
		define('BX_WITH_ON_AFTER_EPILOG', true);
		define('BX_NO_ACCELERATOR_RESET', true);

		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
		global $DB;

		$isTableEmptyQuery = $DB->Query("select count(*) as count from b_user_option uo join b_user_group ug on ug.user_id = uo.user_id where ug.group_id = 24 and (name like 'left_menu_first_page%' or name like 'left_menu_sorted_items%');");
		$tableRes = $isTableEmptyQuery->fetch()['count'];

		if ($tableRes > 0) {
			$DB->Query("delete from b_user_option where user_id in (select user_id from b_user_group where group_id = 24) and (name like 'left_menu_first_page%' or name like 'left_menu_sorted_items%');");
			$managedCache = \Bitrix\Main\Application::getInstance()->getManagedCache();
			$managedCache->cleanAll();
		}

		return '\SFZ\Custom\Exchange\Kiosk::resetLeftMenuSequence();';
	}
	
}