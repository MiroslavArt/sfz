<?php
namespace iTrack\BpExtension\EventHandlers;

use Bitrix\Bizproc\Workflow\Entity\WorkflowInstanceTable;
use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;

class Bizproc
{
	public static function onTaskAdd($taskId, $arFields)
	{
	    \Bitrix\Main\Loader::includeModule('pull');
		$params = unserialize($arFields['PARAMETERS']);

		if(!empty($params['DOCUMENT_ID'])) {
			list($module, $entity, $documentId) = \CBPHelper::ParseDocumentId($params['DOCUMENT_ID']);
			if($module === 'crm') {
				$entityTypeId = null;
				$entityId = null;
				switch($entity) {
					case 'CCrmDocumentDeal':
					case 'CCrmDocumentLead':
					case 'CCrmDocumentContact':
					case 'CCrmDocumentCompany':
					\CPullStack::AddShared(['module_id' => 'crm', 'command' => 'crm_bizproc_task_create', 'params' => ['TAG' => 'CRM_BP_TASK_'.$documentId]]);
						break;
				}
			}
            
		}
	}
}