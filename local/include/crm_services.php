<?php
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\DI;

class MyFactory extends Service\Factory\Dynamic 
{
    public function getUpdateOperation(Item $item, Context $context = null): Operation\Update
    {
        $operation = parent::getUpdateOperation($item, $context);
        \Bitrix\Main\Diag\Debug::writeToFile($userId, "ops".date("d.m.Y G.i.s"), "__stzexp.log");
        return $operation->addAction(
            Operation::ACTION_BEFORE_SAVE,
            new class extends Operation\Action {
                public function process(Item $item): Result
                {
                    $result = new Result();
                    $userId = Service\Container::getInstance()->getContext()->getUserId();
                    
                    \Bitrix\Main\Diag\Debug::writeToFile($userId, "dataexp".date("d.m.Y G.i.s"), "__stzexp.log");
                    
                    return $result;
                }
            }
        );
    }
}
$type = new \Bitrix\Crm\Model\Dynamic\Type('134'); 

$container = new MyFactory($type);

DI\ServiceLocator::getInstance()->addInstance('crm.service.factory.dynamic', $container);
