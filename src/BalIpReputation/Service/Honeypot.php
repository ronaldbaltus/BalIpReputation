<?php
namespace BalIpReputation\Service;

use BalIpReputation\Options\ModuleOptions;

class HoneyPot extends IpReputationServiceBase
{  
    protected $MEMCACHE_KEY = "BalIpReputationHoneypot";
  
    /**
     * Results per IP
     * @var Array
     */
    private $ips = array();
    
    /**
     * Keep track if the data is changed
     * @var Boolean
     */
    private $dataChanged = false;
  
    /**
     * Contructor
     * 
     * @param ModuleOptions
     */
    public function __construct(ModuleOptions $options = null)
    {
        parent::__construct($options);
        
        $ips = $this->memcache()->get($this->MEMCACHE_KEY);
        
        if (is_array($ips)) {
            $this->ips = $ips;
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        if (!$this->dataChanged) {
            return;
        }
        
        // store data in memcache for 24 hour.
        $this->memcache()->set($this->MEMCACHE_KEY, $this->ips, MEMCACHE_COMPRESSED, 86400);
    }
    
    /**
     * Retrieve data from honeypot. False is not reported!
     * @param string ip
     * @return boolean
     */
    public function getIP($ip)
    {
        if (isset($this->ips[$ip])) {
            return $this->ips[$ip];
        }
        
        // The url to the senderbase
        $url = $this->getUrl($ip);
        
        // Get the html
        exec("wget -qO- {$url}", $output);
        $html = implode("\n", $output);
        
        $this->dataChanged = true;
        
        // Do they even have detect us
        $result = preg_match_all('/We don\'t have data on this IP currently/ims', $html);

        if ($result === false) {
            throw new \Exception("Unable to parse answer from honeypot!");
        }
        
        $this->ips[$ip] = $result == 0;
         
        return $this->ips[$ip];
    }
    
    /**
     * Get senderbase url
     * 
     * @param mixed $ip
     * @return string $url
     */
    public function getUrl($ip)
    {
        return "http://www.projecthoneypot.org/ip_{$ip}";
    }
}
?>
