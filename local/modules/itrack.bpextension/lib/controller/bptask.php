<?php

namespace iTrack\BpExtension\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;

class BPTask extends Controller
{
    public function configureActions()
    {
        return [
            'list' => []
        ];
    }

    public function listAction($entityType, $entityId)
    {
        Loader::includeModule('bizproc');
        Loader::includeModule('crm');
        global $USER;
        $currentUserId = $USER->GetID();
        $isAdmin = $USER->IsAdmin();

        $documentId = '';
        $entity = '';
        switch($entityType) {
            case \CCrmOwnerType::Lead:
                $documentId = \CCrmOwnerType::LeadName.'_'.$entityId;
                $entity = 'CCrmDocumentLead';
                break;
            case \CCrmOwnerType::Deal:
                $documentId = \CCrmOwnerType::DealName.'_'.$entityId;
                $entity = 'CCrmDocumentDeal';
                break;
            case \CCrmOwnerType::Contact:
                $documentId = \CCrmOwnerType::ContactName.'_'.$entityId;
                $entity = 'CCrmDocumentContact';
                break;
            case \CCrmOwnerType::Company:
                $documentId = \CCrmOwnerType::CompanyName.'_'.$entityId;
                $entity = 'CCrmDocumentCompany';
                break;
        }

        $result = [];

        if(!empty($entity) && !empty($documentId)) {
            $arFilter = array(
                "USER_ID" => $currentUserId,
                'USER_STATUS' => \CBPTaskUserStatus::Waiting,
                'DOCUMENT_ID' => $documentId,
                'ENTITY' => $entity
            );

            $dbRecordsList = \CBPTaskService::GetList(
                [],
                $arFilter,
                false,
                false,
                ["ID", "WORKFLOW_ID", "ACTIVITY", "ACTIVITY_NAME", "MODIFIED", "OVERDUE_DATE", "NAME", "DESCRIPTION", "PARAMETERS",
                    'STATUS', 'DOCUMENT_NAME', 'WORKFLOW_TEMPLATE_ID', 'MODULE_ID', 'ENTITY', 'DOCUMENT_ID', 'WORKFLOW_TEMPLATE_NAME', 'WORKFLOW_TEMPLATE_TEMPLATE_ID']
            );

            while ($arRec = $dbRecordsList->Fetch()) {
                $result[] = $arRec;
            }
        }

        return $result;
    }
}