<?php
namespace BalIpReputation\Service;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class HoneypotFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('BalIpReputation\Options\ModuleOptions');
        
        return new Honeypot($options);
    }
}
