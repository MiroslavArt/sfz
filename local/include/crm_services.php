<?php
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\DI;
use Bitrix\Crm\Service\Factory;
use SFZ\Custom\Helpers\Utils;

$container = new class extends Service\Container {
    public function getFactory(int $entityTypeId): ?Factory
    {
        // it is the same as
        // DI\ServiceLocator::getInstance()->addInstance('Crm.Service.Factory.Dynamic.150',$factory);
        if ($entityTypeId == TYPE1ID)
        {
            $type = $this->getTypeByEntityTypeId($entityTypeId);
            // our new custom factory class
            $factory = new class($type) extends Factory\Dynamic {
                public function getUpdateOperation(Item $item, Context $context = null): Operation\Update
                {
                    $operation = parent::getUpdateOperation($item, $context);
                    return $operation->addAction(
                        Operation::ACTION_BEFORE_SAVE,
                        new class extends Operation\Action {
                            public function process(Item $item): Result
                            {
                                $result = new Result();
                                $typedata = $item->getData(); 

                                if($item->isChangedTitle()) {
                                    $PROPS['name1'] = $typedata['TITLE']; 
                                }
                                if($item->isChanged(TYPE1UFENG)) {
                                    $PROPS['name1eng'] = $typedata[TYPE1UFENG]; 
                                }


                                foreach($PROPS as $key=>$item) {
                                    $data = [
                                        'ACTIVE' => 'Y',
                                        'NAME' => $key,
                                        'PROPERTY_VALUES' => [
                                            'NOVOE_ZNACHENIE'=> $item,
                                            'SKVOZNAYA_KOMPANIYA_1' => $typedata['ID'],
                                            'DATA_IZMENENIYA' => ConvertTimeStamp(time(), "FULL") 
                                        ]
                                    ];
                                    $id = Utils::createIBlockElement(makeexportIB, $data, []);
                                }
                                
                                return $result;
                            }
                        }
                    );
                }    
            };
            return $factory;
        } elseif($entityTypeId == TYPE2ID) {
            $type = $this->getTypeByEntityTypeId($entityTypeId);
            // our new custom factory class
            $factory = new class($type) extends Factory\Dynamic {
                public function getUpdateOperation(Item $item, Context $context = null): Operation\Update
                {
                    $operation = parent::getUpdateOperation($item, $context);
                    return $operation->addAction(
                        Operation::ACTION_BEFORE_SAVE,
                        new class extends Operation\Action {
                            public function process(Item $item): Result
                            {
                                $result = new Result();
                                $typedata = $item->getData(); 

                                if($item->isChangedTitle()) {
                                    $PROPS['name2'] = $typedata['TITLE']; 
                                }
                                if($item->isChanged(TYPE2UFENG)) {
                                    $PROPS['name2eng'] = $typedata[TYPE2UFENG]; 
                                }
                                if($item->isChanged(TYPE2UFMANSYPLY)) {
                                    $user = Utils::getUserbycondition(array('=ID' =>$typedata[TYPE2UFMANSYPLY]));
                                    if($user) {
                                        $PROPS['managerplyemail'] = $user['EMAIL'];
                                    }
                                }
                                if($item->isChanged(TYPE2UFMANLAM)) {
                                    $user = Utils::getUserbycondition(array('=ID' =>$typedata[TYPE2UFMANLAM]));
                                    if($user) {
                                        $PROPS['managerplyemail'] = $user['EMAIL'];
                                    }
                                }
                                
                                //$userId = Service\Container::getInstance()->getContext()->getUserId();
                                //\Bitrix\Main\Diag\Debug::writeToFile($item->getData(), "dataexp1".date("d.m.Y G.i.s"), "__stzexp.log");
                                //\Bitrix\Main\Diag\Debug::writeToFile($item->isChangedTitle(), "tchanged".date("d.m.Y G.i.s"), "__stzexp.log");
                                //\Bitrix\Main\Diag\Debug::writeToFile($item->isChanged('UF_CRM_3_1642771199'), "ufchanged".date("d.m.Y G.i.s"), "__stzexp.log");

                                foreach($PROPS as $key=>$item) {
                                    $data = [
                                        'ACTIVE' => 'Y',
                                        'NAME' => $key,
                                        'PROPERTY_VALUES' => [
                                            'NOVOE_ZNACHENIE'=> $item,
                                            'SKVOZNAYA_KOMPANIYA_2' => $typedata['ID'],
                                            'DATA_IZMENENIYA' => ConvertTimeStamp(time(), "FULL") 
                                        ]
                                    ];
                                    $id = Utils::createIBlockElement(makeexportIB, $data, []);
                                }
                                
                                return $result;
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


