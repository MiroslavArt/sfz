<?php

namespace Notamedia\Analytics\Reports\Filters;

use Bitrix\Crm\Filter\DealSettings;
use Bitrix\Crm\Filter\Factory;
use Bitrix\Crm\Integration\Report\Dashboard\Sales\SalesDynamic;
use Bitrix\Crm\Integration\Report\Filter\Base as BaseFilter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Filter\DateType;

class SfzPipelineFilter extends BaseFilter
{
	/**
	 * @return array
	 */
	public static function getFieldsList()
	{
		$fieldsList = parent::getFieldsList();

		$userPermissions = \CCrmPerms::getCurrentUserPermissions();
		$dealFilter = Factory::createEntityFilter(
			new DealSettings(
				array(
					'ID' => SalesDynamic::BOARD_KEY,
					'categoryID' => -1,
					'categoryAccess' => array(
						'READ' => \CCrmDeal::getPermittedToReadCategoryIDs($userPermissions),
					),
					'flags' => DealSettings::FLAG_NONE
				)
			)
		);

		$fields = $dealFilter->getFields();

		$disabledFieldKeys = [
			'ACTIVITY_COUNTER',
			'TRACKING_SOURCE_ID',
			'TRACKING_CHANNEL_CODE',
			'PRODUCT_ROW_PRODUCT_ID',
			'STAGE_SEMANTIC_ID',
			'STAGE_ID_FROM_HISTORY',
			'STAGE_ID_FROM_SUPPOSED_HISTORY',
			'STAGE_SEMANTIC_ID_FROM_HISTORY',
			'COMMENTS',
            'ROBOT_DEBUGGER',
            'ORDER_SOURCE',
            'STAGE_ID_FROM_HISTORY',
            'ACTIVE_TIME_PERIOD',
            'UTM_TERM',
            'UTM_CONTENT',
            'UTM_CAMPAIGN',
            'UTM_MEDIUM',
            'UTM_SOURCE',
            'WEBFORM_ID',
            'ORIGINATOR_ID',
            'MODIFY_BY_ID',
            'CREATED_BY_ID',
            'DATE_MODIFY',
            'DATE_CREATE',
            'TYPE_ID',
            'CONTACT_FULL_NAME',
            'EVENT_ID',
            'EVENT_DATE',
            'BEGINDATE',
            'PAYMENT_PAID',
            'PAYMENT_STAGE',
            'DELIVERY_STAGE',
            'STAGE_SEMANTIC_ID',
            'CLOSED',
            'CLOSEDATE',
            'SOURCE_ID',
            'IS_REPEATED_APPROACH',
            'IS_RETURN_CUSTOMER',
            'IS_NEW',
            'PROBABILITY',
			'CONTACT_ID'
		];
        $ufarr = [THROUGHCOMPANYDEAL, MARKETDEAL];
		foreach ($fields as $field)
		{
			$field = $field->toArray();
			
			if (in_array($field['id'], $disabledFieldKeys))
			{
				continue;
			}

            if (preg_match('/UF_/', $field['id'])) {
                if (!in_array($field['id'], $ufarr)) {
                    continue;
                } 

                if($field['id']==THROUGHCOMPANYDEAL) {
                    $field['params']['multiple'] = 'Y';
                }
            }

			if ($field['id'] === 'CATEGORY_ID')
			{
				$field['params']['multiple'] = 'N';
			}

			if ($field['id'] === 'COMPANY_ID')
			{
				$field['params']['multiple'] = 'Y';
			}

            $field['id'] = 'FROM_DEAL_'.$field['id'];
			$field['name'] = $field['name'].' '.Loc::getMessage('CRM_REPORT_SALES_DYNAMIC_BOARD_FILTER_DEAL_FIELDS_POSTFIX');
			if (isset($field['type']) && $field['type'] === 'custom_entity')
			{
				$field['html'] = str_replace(
					$field['selector']['DATA']['FIELD_ID'],
					'FROM_DEAL_'.$field['selector']['DATA']['FIELD_ID'],
					$field['html']
				);
				$field['html'] = str_replace(
					$field['selector']['DATA']['ID'],
					'from_deal_'.$field['selector']['DATA']['ID'],
					$field['html']
				);
				$field['selector']['DATA']['ID'] = 'from_deal_'.$field['selector']['DATA']['ID'];
				$field['selector']['DATA']['FIELD_ID'] = 'FROM_DEAL_'.$field['selector']['DATA']['FIELD_ID'];
			}
			$fieldsList[] = $field;
		}

		return $fieldsList;
	}

	/**
	 * @return array
	 */
	public static function getPresetsList()
	{
		$presets = parent::getPresetsList();

		$presets['filter_last_30_day'] = [
			'name' => Loc::getMessage('CRM_REPORT_SALES_DYNAMIC_LAST_30_DAYS_FILTER_PRESET_TITLE'),
			'fields' => array(
				'TIME_PERIOD_datesel' => DateType::LAST_30_DAYS,
				'FROM_DEAL_CATEGORY_ID' => "0"
			),
			'default' => true,
		];

		$presets['filter_current_month']['default'] = false;

		\Bitrix\Main\Diag\Debug::writeToFile($presets, "dataexp1".date("d.m.Y G.i.s"), "__stzexp.log");

		return $presets;
	}

}