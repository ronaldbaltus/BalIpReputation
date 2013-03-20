<?php
namespace BalIpReputation\Service;

use BalIpReputation\Options\ModuleOptions;

abstract class IpReputationServiceBase
{
    /**
     * The memcache object
     * @var Memcache
     */
    private $memcache = null;
    
    
    /**
     * Stored options
     * @var ModuleOptions
     */
    private $options = null;
    
    /**
     * Contructor
     * 
     * @param ModuleOptions
     */
    public function __construct(ModuleOptions $options = null)
    {
        $this->options = $options;
    }
    
    /**
     * Get access to the memcache
     * @return \Memcache
     */
    protected function memcache()
    {
        if (is_object($this->memcache)) return $this->memcache;
        
        $this->memcache = new \Memcache();
        $this->memcache->connect("localhost");
        return $this->memcache;
    }
    
    /**
     * Get the results per IP.
     * 
     * @param string $ip
     * @return mixed
     */
    abstract public function getIP($ip);

}
  
?>
