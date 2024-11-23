<?php
namespace iTrack\BpExtension\EventHandlers;

use Bitrix\Bizproc\Workflow\Entity\WorkflowInstanceTable;
use Bitrix\Main\Loader;
use SFZ\Custom\Helpers\Utils;
use Bitrix\Crm\Service;

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
			if(!empty($arFields['WORKFLOW_ID'])) {
                $wfid = $arFields['WORKFLOW_ID'];
                $workflowState = \CBPStateService::getWorkflowState($wfid);
                
				if($workflowState['TEMPLATE_ID']==WFFILTER && $params['REQUEST'][0]['Name']==WFCODEFILTER) {
                    $leadid = preg_replace("/[^\d]/", '', $documentId);
					
                    if(Loader::includeModule('crm'))
                    {
                       $arLead = \CCrmLead::GetByID($leadid);
					   $user = Utils::getUserbycondition(array('=ID' =>$arLead['ASSIGNED_BY_ID']));
					   
					   
					   $factory = Service\Container::getInstance()->getFactory(TYPE2ID);

					   $filter[TYPE2UFACTIVE] = 1; 
					   if(in_array(TYPE2UFMANSYPLYD, $user['UF_DEPARTMENT']) && !in_array(TYPE2UFMANLAMD, $user['UF_DEPARTMENT'])) {
							$filter['!'.TYPE2UFMANSYPLY] = false; 	
					   } elseif(!in_array(TYPE2UFMANSYPLYD, $user['UF_DEPARTMENT']) && in_array(TYPE2UFMANLAMD, $user['UF_DEPARTMENT'])) {
						    $filter['!'.TYPE2UFMANLAM] = false; 
					   } elseif(in_array(TYPE2UFMANSYPLYD, $user['UF_DEPARTMENT']) && in_array(TYPE2UFMANLAMD, $user['UF_DEPARTMENT'])) {
						    $filter['!'.TYPE2UFMANSYPLY] = false; 
						    $filter['!'.TYPE2UFMANLAM] = false; 
				   	   } else {
						   $filter['!'.TYPE2UFMANSYPLY] = false; 
						   $filter['!'.TYPE2UFMANLAM] = false; 
					   }
					   
					
					   $items = $factory->getItems([
						//'select' => [],
							'filter' => $filter
					   ]);
					   
                       $skvoz = [];
                       if(!empty($items)) {
                           foreach ($items as $item) {
                               //$params['REQUEST'][0]['Options'][$item['ID']] = $item['TITLE'];
							   $skvoz[$item['ID']] =  $item['TITLE'];
                           }
                       }

					   asort($skvoz);

					   foreach ($skvoz as $key=>$item) {
							$params['REQUEST'][0]['Options'][$key] = $item;
					   }

                       $update = \CBPTaskService::Update($taskId, array(
                           'PARAMETERS' => $params
                       ));
                    }
                }
            
			}
		}
	}
}