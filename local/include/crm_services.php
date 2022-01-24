<?php
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\DI;
use Bitrix\Crm\Service\Factory;

$container = new class extends Service\Container {
    public function getFactory(int $entityTypeId): ?Factory
    {
        \Bitrix\Main\Diag\Debug::writeToFile($entityTypeId, "ops".date("d.m.Y G.i.s"), "__stzexp.log");
        // it is the same as
        // DI\ServiceLocator::getInstance()->addInstance('Crm.Service.Factory.Dynamic.150',$factory);
        /*if ($entityTypeId === TYPE2ID)
        {
            $type = $this->getTypeByEntityTypeId($entityTypeId);
            // our new custom factory class
            $factory = new class($type) extends Factory\Dynamic {
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
            };
            return $factory;
        }*/
        return parent::getFactory($entityTypeId);
    }
};
// here we change the container
DI\ServiceLocator::getInstance()->addInstance('crm.service.container', $container);


