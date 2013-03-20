<?php
namespace BalIpReputation\Options;

use \Zend\StdLib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * IP addresses to check
     * @var Array
     */
    private $ipaddresses = array();
    
    /**
     * Set the ipaddresses.
     * @param array
     */
    public function setIpAddresses($addresses) 
    {
        $this->ipaddresses = $addresses;    
    }
    
    /**
     * Get the ip addresses
     * @return array
     */
    public function getIpAddresses()
    {
        return $this->ipaddresses;
    }
}
  
?>
