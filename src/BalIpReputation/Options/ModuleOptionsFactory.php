<?php
namespace BalIpReputation\Options;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get("Config");
        $balConf = isset($config["bal_ip_reputation"]) ? $config["bal_ip_reputation"] : array();
        
        return new ModuleOptions($balConf);
    }
}
