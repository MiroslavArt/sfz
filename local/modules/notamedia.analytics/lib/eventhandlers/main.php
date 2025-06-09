<?php

namespace Notamedia\Analytics\EventHandlers;

use Bitrix\Crm\Integration\Report\Dashboard\Customers\RegularCustomers;
use Bitrix\Crm\Integration\Report\Filter\Customers\DealBasedFilter;
use Bitrix\Crm\Integration\Report\Dashboard\MyReports;
use Bitrix\Crm\Integration\Report\Filter\MyReportsFilter;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;
use Bitrix\Crm\Integration\Report\Filter\Deal\SalesDynamicFilter;
use Bitrix\Crm\Integration\Report\Dashboard\Sales\SalesDynamic;

use Notamedia\Analytics\Reports\Boards;
use Notamedia\Analytics\Reports\Views;
use Notamedia\Analytics\Reports\Filters;

class Main
{
    public static function onProlog()
    {
        $eventManager = EventManager::getInstance();

        define('THROUGHCOMPANYDEAL', \COption::GetOptionString('notamedia.analytics', 'pipeline_ufthtoughcomp'));
        define('MARKETDEAL', \COption::GetOptionString('notamedia.analytics', 'pipeline_trade_direction'));
        define('ACTVOLUMEDEAL', \COption::GetOptionString('notamedia.analytics', 'pipeline_act_volume'));
        define('PLANVOLUMEDEAL', \COption::GetOptionString('notamedia.analytics', 'pipeline_plan_volume'));

        Loader::includeModule('crm');
        Loader::includeModule('report');
        //TODO: CRM Analytics menu node with links
        /* menu group */
        /*
        $eventManager->addEventHandler("report", "onAnalyticPageBatchCollect", function (){
            $batchList = [];

            $batch = new \Bitrix\Report\VisualConstructor\AnalyticBoardBatch();
            $batch->setKey('custom_my_reports'); //kernel ID for new node
            $batch->setTitle('My Google data reports');
            $batch->setOrder(300);

            if (method_exists($batch, 'setGroup'))
            {
                $batch->setGroup('sales_general');
            }

            $batchList[] = $batch;

            return $batchList;
        });*/

        /* menu point in menu group */
        $eventManager->addEventHandler("report", "onAnalyticPageCollect", function (){

            $analyticPageList = [];


            /* added board 3 for testing */
            $reportPage = new \Bitrix\Report\VisualConstructor\AnalyticBoard(Boards\NotaBoard::BOARD_KEY);
            $reportPage->setTitle(Boards\NotaBoard::getBoardTitle());
            $reportPage->setBatchKey('my_reports'); //node id in menu
            if (method_exists($reportPage, 'setGroup'))
            {
                $reportPage->setGroup('sales_general');
            }
            //$reportPage->setFilter(new MyReportsFilter(Boards\NotaBoard::getPanelGuid()));
            //$reportPage->setFilter(new SalesDynamicFilter(SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ"));
            $reportPage->setFilter(new Filters\SfzPipelineFilter(SalesDynamic::BOARD_KEY . "_" . SalesDynamic::VERSION . "_SFZ"));
            $analyticPageList[] = $reportPage;
            /**/

            return $analyticPageList;
        });


        /* dashboards - links from points in menu */
        $eventManager->addEventHandler("report", "onDefaultBoardsCollect", function (){
            $dashboards = [];
            $dashboards[] = Boards\NotaBoard::get();
            return $dashboards;
        });

        //
        /* view for handler */
        ///*
        $eventManager->addEventHandler("report", "onReportViewCollect", function (){
            $result = [];
            $view = new Views\NotaReport();
            $result[] = $view;
            return $result;
        });
        //*/
        $eventManager->addEventHandler('crm','OnAfterCrmDealUpdate', 
            ['\Notamedia\Analytics\EventHandlers\Crm','OnAfterCrmDealUpdate']);

    }
}