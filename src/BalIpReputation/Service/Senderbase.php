<?php
namespace BalIpReputation\Service;

use BalIpReputation\Options\ModuleOptions;

class Senderbase extends IpReputationServiceBase
{  
    protected $MEMCACHE_KEY = "BalIpReputationSenderBase";
  
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
     * Retrieve data from sender base
     * @param string ip
     * @return string|boolean
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
        
        $matches = array();
        
        // find the score
        preg_match_all(
            '/Senderbase reputation score<\/td>.*?<td[a-zA-Z="%0-9\' ]*>.*?([a-zA-Z]+).*?<\/td>/ims',
            $html,
            $matches
        );
        
        $this->dataChanged = true;
        
        if (!isset($matches[1][0])) {
            $this->ips[$ip] = false;
        } else {
            $this->ips[$ip] = $matches[1][0];
        }
        
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
        return "http://www.senderbase.org/senderbase_queries/detailip?search_string={$ip}";
    }
}
?>
