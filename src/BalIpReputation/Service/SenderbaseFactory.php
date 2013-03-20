<?php
namespace BalIpReputation\Service;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class SenderbaseFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('BalIpReputation\Options\ModuleOptions');
        
        return new Senderbase($options);
    }
}
