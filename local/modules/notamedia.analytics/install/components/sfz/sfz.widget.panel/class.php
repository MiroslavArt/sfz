<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use SFZ\Custom\Helpers\Utils;
use \Bitrix\Main\Loader;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Crm\Integration\Report\Filter\Deal\SalesDynamicFilter;
use Bitrix\Crm\Integration\Report\Dashboard\Sales\SalesDynamic;
use Bitrix\Crm\History\Entity\DealStageHistoryTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Entity\ExpressionField;
use Notamedia\Analytics\Reports\Filters;

class WidgetPanel extends CBitrixComponent 
    implements \Bitrix\Main\Engine\Contract\Controllerable
{
    
    private $filterkey; 

    const FILTER_FIELDS_PREFIX = 'FROM_DEAL_';
    const DEFAULT_CAT_ID = 2;
    const DEAL_FIELD = 'deals';
    const STATUS_FIELD = 'status';
    const DEF_COLOR = '#47E4C2';

    public function configureActions()
    {
        //если действия не нужно конфигурировать, то пишем просто так. И будет конфиг по умолчанию 
        return [];
    }

    public function chartgetAction()
    {
        $prefilter = $this->getpreFilter(); 
        $finalFilter = $this->getfinFilter($prefilter); 
        $stages = $this->getDealStages();
        $query = $this->makeRequest($finalFilter, $stages);
        return $query['items'];
    }
    
    public function onPrepareComponentParams($arParams)
    {
        // параметры для фильтров тут
        $this->filterkey = SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ";
        //$this->filterkey = $arParams['FILTERKEY'];
        //$this->contractId = $arParams['CONTRACT_ID'];

        //$this->filtervalues = $arParams['FILTER'];
        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        Loader::includeModule('crm');

        $this->filterkey = SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ";

        $arResult =& $this->arResult;

        $prefilter = $this->getpreFilter(); 
        $finalFilter = $this->getfinFilter($prefilter); 
        $stages = $this->getDealStages();
        $query = $this->makeRequest($finalFilter, $stages);
        $query['category'] = $this->category;

        $arResult['PIPELINE_DATA'] = $query; 

        //\Bitrix\Main\Diag\Debug::writeToFile($arResult['accidents'], "dataexp".date("d.m.Y G.i.s"), "__debug.log");
        $this->includeComponentTemplate();
    }

    private function getpreFilter() {
        //$filter = new SalesDynamicFilter(SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ");
        //$filter = new SalesDynamicFilter($this->filterkey);
        $filter = new Filters\SfzPipelineFilter($this->filterkey);
        
        $filterid = $filter->getFilterParameters()['FILTER_ID'];

        $filterOption = new Filter\Options($filterid);

        $filterfields = $filter->getFieldsList();

        $this->arResult['FILTER_FIELDS'] = $filterfields; 
        //$options = []; 
        $rawfilter = $filterOption->getFilter();
        //$options[] = $filter->getFieldsList();
        //echo '<pre>';  var_dump($filterOption); echo '</pre>';
        $rawfilter = $this->mutateFilterParameter($rawfilter, $filterfields);
        return $rawfilter; 
    }

    private function getfinFilter(array $prefilter) {
        $finfilter = [];
        if(isset($prefilter['ID'])) {
            $finfilter['OWNER_ID'] = $prefilter['ID']['value'];
        }
        if(isset($prefilter['TIME_PERIOD'])) {
            $finfilter['>=EFFECTIVE_DATE'] = $prefilter['TIME_PERIOD']['from'];
            $finfilter['<=EFFECTIVE_DATE'] = $prefilter['TIME_PERIOD']['to'];
        }
        if(isset($prefilter['CATEGORY_ID'])) {
            $finfilter[static::DEAL_FIELD.'.CATEGORY_ID'] = $prefilter['CATEGORY_ID']['value'];
        } else {
            $finfilter[static::DEAL_FIELD.'.CATEGORY_ID'] = static::DEFAULT_CAT_ID; 
        }
        $this->category = $finfilter[static::DEAL_FIELD.'.CATEGORY_ID'];
        if(isset($prefilter['TITLE'])) {
            $finfilter[static::DEAL_FIELD.'.TITLE'] = $prefilter['TITLE']['value'];
        } 
        if(isset($prefilter['ASSIGNED_BY_ID'])) {
            $assigned = [];

            foreach($prefilter['ASSIGNED_BY_ID']['value'] as $value) {
                if(preg_match("/[0-9]/", $value)) {
                    $assigned[] = $value;
                }
            } 
            if(!empty($assigned)) {
                $finfilter[static::DEAL_FIELD.'.ASSIGNED_BY_ID'] = $assigned;
            }
        } 
        if(isset($prefilter['COMPANY_ID'])) {
            $finfilter[static::DEAL_FIELD.'.COMPANY_ID'] = $prefilter['COMPANY_ID']['value'];
        }
        if(isset($prefilter['CURRENCY_ID'])) {
            $finfilter[static::DEAL_FIELD.'CURRENCY_ID'] = $prefilter['CURRENCY_ID']['value'];
        }
        if(isset($prefilter['OPPORTUNITY'])) {
            $finfilter[static::DEAL_FIELD.'.>=OPPORTUNITY'] = $prefilter['OPPORTUNITY']['from'];
            $finfilter[static::DEAL_FIELD.'.<=OPPORTUNITY'] = $prefilter['OPPORTUNITY']['to'];
        }
        if(isset($prefilter[THROUGHCOMPANYDEAL])) {
            $finfilter[static::DEAL_FIELD.'.'.THROUGHCOMPANYDEAL] = $prefilter[THROUGHCOMPANYDEAL]['value'];
        }
        if(isset($prefilter[MARKETDEAL])) {
            $finfilter[static::DEAL_FIELD.'.'.MARKETDEAL] = $prefilter[MARKETDEAL]['value'];
        }
        /*if(isset($prefilter['CLOSED'])) {
            if($prefilter['CLOSED']['VALUE']=='Y') {
                $finfilter['STAGE']['TYPE_ID'] = [3];
            } elseif($prefilter['CLOSED']['VALUE']=='N') {
                $finfilter['STAGE']['TYPE_ID'] = [1,2];
            }
        } else {
            $finfilter['STAGE']['TYPE_ID'] = [1,2,3];
        }*/
        
        /*if(isset($prefilter['CLOSEDATE'])) {
            $finfilter['DEAL']['>=CLOSEDATE'] = $prefilter['CLOSEDATE']['from'];
            $finfilter['DEAL']['<=CLOSEDATE'] = $prefilter['CLOSEDATE']['to'];
        }*/ 
        
        
        /*if(isset($prefilter['PROBABILITY'])) {
            $finfilter['DEAL']['>=PROBABILITY'] = $prefilter['PROBABILITY']['from'];
            $finfilter['DEAL']['<=PROBABILITY'] = $prefilter['PROBABILITY']['to'];
        } 
        if(isset($prefilter['IS_NEW'])) {
            $finfilter['DEAL']['IS_NEW'] = $prefilter['IS_NEW']['value'];
        } */
       return $finfilter;

    }

    private function getDealStages() {
        $stages = \CCrmDeal::GetAllStageNames($this->category);
        return $stages;
    }

    private function makeRequest($finalFilter, $stages)
    {
        if(!$finalFilter['>=EFFECTIVE_DATE'] || !$finalFilter['<=EFFECTIVE_DATE']) {
            return []; 
        } else {
            $query = new Query(DealStageHistoryTable::getEntity());
            
            $query->registerRuntimeField( // поле element как ссылка на таблицу b_iblock_element
                static::STATUS_FIELD,
                array(
                   'data_type' => 'Bitrix\Crm\StatusTable',
                    'reference' => array('=this.STAGE_ID' => 'ref.STATUS_ID'),
                )
            );

            $query->registerRuntimeField( // поле element как ссылка на таблицу b_iblock_element
                static::DEAL_FIELD,
                array(
                    'data_type' => 'Bitrix\Crm\DealTable',
                    'reference' => array('=this.OWNER_ID' => 'ref.ID'),
                )
            );
    
            $query->setSelect(array('STAGE_ID', 'STAGE_SEMANTIC_ID', 'STAGE_NAME'=> static::STATUS_FIELD.'.NAME', 'COLOR' => static::STATUS_FIELD.'.COLOR'));
            $query->addSelect('COUNTDEALS');
            $query->registerRuntimeField('', new ExpressionField('COUNTDEALS', 'COUNT(DISTINCT OWNER_ID)'));
            $query->addSelect('SUMPLAN');
            $query->registerRuntimeField('', new ExpressionField('SUMPLAN', 'SUM(%s)', static::DEAL_FIELD.'.'.PLANVOLUMEDEAL));
            $query->addSelect('SUMACT');
            $query->registerRuntimeField('', new ExpressionField('SUMACT', 'SUM(%s)', static::DEAL_FIELD.'.'.ACTVOLUMEDEAL));
            $query->addFilter('IS_LOST', 'N');
            foreach($finalFilter as $key => $value) {
                $query->addFilter($key, $value);
            }
            $dbResult = $query->exec();

            $resultarr = [];

            //echo "<pre>";
            //print_r($stages);
            //echo "</pre>";

            $ary = $dbResult->fetchAll();

            $newary = [];

            foreach($ary as $value) {
                $newary[$value['STAGE_ID']] = [
                    'STAGE_SEMANTIC_ID' => $value['STAGE_SEMANTIC_ID'],
                    'COLOR' => $value['COLOR'],
                    'SUMPLAN' => $value['SUMPLAN'],
                    'SUMACT' => $value['SUMACT'],
                    'COUNTDEALS' => $value['COUNTDEALS']
                ];
            }

            foreach($stages as $key => $value) {
                //echo "<pre>";
                //print_r($key);
                //echo "</pre>";
                
                if(isset($newary[$key])) {
                    if($newary[$key]['STAGE_SEMANTIC_ID']=='P') {

                        $volume = 'Плановый объем: '.($newary[$key]['SUMPLAN']!=false ? $newary[$key]['SUMPLAN'] : 0). 'м3';
                    } else {
                        $volume = 'Фактический объем: '.($newary[$key]['SUMACT']!=false ? $newary[$key]['SUMACT'] : 0). 'м3';
                    }

                    $resultarr['items'][] = ['title'=>$value.'.'.$volume, 'value'=>$newary[$key]['COUNTDEALS']];
                    $resultarr['colors'][] = $newary[$key]['COLOR'] != false ? $newary[$key]['COLOR'] : self::DEF_COLOR;
                }
            }

            return $resultarr; 
        }
    }
    

    private function mutateFilterParameter($filterParameters, array $fieldList)
    {
        $mutatedFilterParameters = [];
        $preparedFieldList = [];

        foreach ($fieldList as $field)
        {
            if ($field['id'] === 'TIME_PERIOD' || $field['id'] === 'PREVIOUS_PERIOD')
            {
                $preparedFieldList[$field['id']] = [
                    'type' => isset($field['type']) ? $field['type'] : 'none',
                    'field' => $field
                ];
                continue;
            }

            if (mb_strpos($field['id'], static::FILTER_FIELDS_PREFIX) === 0)
            {
                $newFieldKeyList = explode(static::FILTER_FIELDS_PREFIX, $field['id']);
                $newFieldKey = $newFieldKeyList[1];
                $preparedFieldList[$newFieldKey] = [
                    'type' => isset($field['type']) ? $field['type'] : 'none',
                    'field' => $field
                ];
            }
        }

        foreach ($filterParameters as $key => $value)
        {
            if (mb_strpos($key, 'TIME_PERIOD') === 0)
            {
                $mutatedFilterParameters[$key] = $value;
                continue;
            }
            if (mb_strpos($key, 'PREVIOUS_PERIOD') === 0)
            {
                $mutatedFilterParameters[$key] = $value;
                continue;
            }


            if (mb_strpos($key, 'FIND') === 0)
            {
                $mutatedFilterParameters[$key] = $value;
                continue;
            }
            
            if (mb_strpos($key, static::FILTER_FIELDS_PREFIX) === 0)
            {
                $newKeyList = explode(static::FILTER_FIELDS_PREFIX, $key);
                $newKey = $newKeyList[1];
                $normalizedKey = $this->extractFieldId($newKey);
                if (isset($preparedFieldList[$normalizedKey]))
                {
                    $mutatedFilterParameters[$newKey] = $value;
                }
            }
        }

        if (empty($mutatedFilterParameters))
        {
            return $mutatedFilterParameters;
        }

        $preparedFieldListForEntityHandler = [];
        foreach ($preparedFieldList as $key => $value)
        {
            $preparedFieldListForEntityHandler[$key] = $value['field'];
        }


        foreach ($preparedFieldList as $fieldId => $preparedField)
        {
            switch ($preparedField['type'])
            {
                case 'none':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'none',
                        'value' => $mutatedFilterParameters[$fieldId]
                    ];
                    }
                    break;
                case 'list':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'list',
                        'value' => $mutatedFilterParameters[$fieldId]
                    ];
                    }
                    break;
                case 'date':
                    if (isset($mutatedFilterParameters[$fieldId . '_from']) && isset($mutatedFilterParameters[$fieldId . '_to']))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'date',
                        'from' => $mutatedFilterParameters[$fieldId.'_from'],
                        'to' =>  $mutatedFilterParameters[$fieldId.'_to'],
                        'datesel' => $mutatedFilterParameters[$fieldId . '_datesel'],
                        'month' => $mutatedFilterParameters[$fieldId . '_month'],
                        'quarter' => $mutatedFilterParameters[$fieldId . '_quarter'],
                        'year' => $mutatedFilterParameters[$fieldId . '_year'],
                        'days' => $mutatedFilterParameters[$fieldId . '_days'],
                    ];
                    }

                    unset($mutatedFilterParameters[$fieldId . '_datesel']);
                    unset($mutatedFilterParameters[$fieldId . '_month']);
                    unset($mutatedFilterParameters[$fieldId . '_quarter']);
                    unset($mutatedFilterParameters[$fieldId . '_year']);
                    unset($mutatedFilterParameters[$fieldId . '_days']);
                    unset($mutatedFilterParameters[$fieldId . '_from']);
                    unset($mutatedFilterParameters[$fieldId . '_to']);
                    break;
                case 'checkbox':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'checkbox',
                        'value' => $mutatedFilterParameters[$fieldId]
                    ];
                    }
                    break;
                case 'number':
                    if (isset($mutatedFilterParameters[$fieldId . '_from']) && isset($mutatedFilterParameters[$fieldId . '_to']))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'diapason',
                        'numsel' => $mutatedFilterParameters[$fieldId . '_numsel'],
                        'from' => $mutatedFilterParameters[$fieldId . '_from'],
                        'to' => $mutatedFilterParameters[$fieldId . '_to']
                    ];
                    }

                    unset($mutatedFilterParameters[$fieldId . '_numsel']);
                    unset($mutatedFilterParameters[$fieldId . '_from']);
                    unset($mutatedFilterParameters[$fieldId . '_to']);
                    break;
                case 'text':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'text',
                        'value' => $mutatedFilterParameters[$fieldId]
                    ];
                    }
                    break;
                case 'custom_entity':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {

                    $oldMutateFilterValue = $mutatedFilterParameters[$fieldId];
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'custom_entity',
                        'selectorEntityType' => 'none',
                        'label' => $mutatedFilterParameters[$fieldId . '_label'],
                        'value' => $mutatedFilterParameters[$fieldId],
                    ];

                    if ($preparedField['field']['selector']['TYPE'] === 'crm_entity')
                    {
                        $encodedValue = $oldMutateFilterValue;
                        $decodedValue  = json_decode($oldMutateFilterValue, true);

                        $mutatedFilterParameters[$fieldId]['selectorEntityType'] = 'crm_entity';
                        $mutatedFilterParameters[$fieldId]['encodedValue'] = $encodedValue;


                        $data = $preparedField['field']['selector']['DATA'];
                        $entityTypeNames = isset($data['ENTITY_TYPE_NAMES']) && is_array($data['ENTITY_TYPE_NAMES'])
                            ? $data['ENTITY_TYPE_NAMES'] : array();

                        $isMultiple = isset($data['IS_MULTIPLE']) ? $data['IS_MULTIPLE'] : false;

                        //TODO change to other structure, ere can potential bug
                        foreach ($entityTypeNames as $entityName)
                        {
                            $entityTypeQty = count($entityTypeNames);
                            if($entityTypeQty > 1)
                            {
                                $entityTypeAbbr = \CCrmOwnerTypeAbbr::ResolveByTypeID(\CCrmOwnerType::ResolveID($entityName));
                                $prefix = "{$entityTypeAbbr}_";
                            }
                            else
                            {
                                $prefix = '';
                            }

                            if(!(isset($decodedValue[$entityName])
                                && is_array($decodedValue[$entityName])
                                && !empty($decodedValue[$entityName]))
                            )
                            {
                                continue;
                            }

                            if(!$isMultiple)
                            {
                                $mutatedFilterParameters[$fieldId]['value'] = "{$prefix}{$decodedValue[$entityName][0]}";
                            }
                            else
                            {
                                $effectiveValues = array();
                                for($i = 0, $qty = count($decodedValue[$entityName]); $i < $qty; $i++)
                                {
                                $effectiveValues[] = "{$prefix}{$decodedValue[$entityName][$i]}";
                                }
                                $mutatedFilterParameters[$fieldId]['value'] = $effectiveValues;
                            }

                        }
                    }


                    unset($mutatedFilterParameters[$fieldId . '_label']);
                    }

                    break;
                case 'dest_selector':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'dest_selector',
                        'label' => $mutatedFilterParameters[$fieldId . '_label'],
                        'value' => preg_replace("/[^0-9]/", '', $mutatedFilterParameters[$fieldId]),
                    ];
                    unset($mutatedFilterParameters[$fieldId . '_label']);
                    }
                    break;
                case 'entity_selector':
                    if (isset($mutatedFilterParameters[$fieldId]))
                    {
                    $mutatedFilterParameters[$fieldId] = [
                        'type' => 'entity_selector',
                        'value' => $mutatedFilterParameters[$fieldId],
                    ];
                    }
                    break;
            }
        }



        return $mutatedFilterParameters;
    }

    private function extractFieldId(string $fieldId)
    {
        $postfixes = [
        '_datesel', '_month', '_quarter', '_year', '_days', // date
        '_numsel', // number
        '_from', '_to', // date and number ranges
        ];
        foreach ($postfixes as $postfix)
        {
            if (mb_substr($fieldId, -($postfixLen = mb_strlen($postfix))) === $postfix)
            {
                return mb_substr($fieldId, 0, -$postfixLen);
            }
        }
        return $fieldId;
    }

}