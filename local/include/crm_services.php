<?php
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\DI;
use Bitrix\Crm\Service\Factory;

define('SUPER_ENTITY_TYPE_ID', 134);

$container = new class extends Service\Container {
    public function getFactory(int $entityTypeId): ?Factory
    {
        // it is the same as
        // DI\ServiceLocator::getInstance()->addInstance('Crm.Service.Factory.Dynamic.150',$factory);
        if ($entityTypeId === SUPER_ENTITY_TYPE_ID)
        {
            $type = $this->getTypeByEntityTypeId($entityTypeId);
            // our new custom factory class
            $factory = new class($type) extends Factory\Dynamic {
                public function getDeleteOperation(Item $item, Context $context = null): Operation\Delete
                {
                    $operation = parent::getDeleteOperation($item, $context);

                    return $operation->addAction(
                        Operation::ACTION_AFTER_SAVE,
                        new class extends Operation\Action {
                            public function process(Item $item): Result
                            {
                                \Bitrix\Main\Diag\Debug::writeToFile('process', "dataexp".date("d.m.Y G.i.s"), "__stzexp.log");
                                $userId = Service\Container::getInstance()->getContext()->getUserId();
                                \Bitrix\Main\Diag\Debug::writeToFile($userId, "dataexp".date("d.m.Y G.i.s"), "__stzexp.log");
                            
                                return new Result();
                            }
                        }
                    );
                }    
            };
            return $factory;
        }

      return parent::getFactory($entityTypeId);
    }
};
// here we change the container
DI\ServiceLocator::getInstance()->addInstance('crm.service.container', $container);


