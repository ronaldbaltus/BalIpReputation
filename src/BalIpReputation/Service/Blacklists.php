<?php
namespace BalIpReputation\Service;

use BalIpReputation\Options\ModuleOptions;

class Blacklists extends IpReputationServiceBase
{
    protected $MEMCACHE_KEY = "BalIpReputationBlacklists";
    
    /**
     * Blacklists we check.
     * 
     * @var array
     */
    private $blackslistdns = array(
        'pam.mrs.kithrup.com', 'access.redhawk.org',
        'all.spamblock.unit.liu.se', 'assholes.madscience.nl',
        'blackholes.five-ten-sg.com', 'blackholes.intersil.net',
        'blackholes.mail-abuse.org', 'blackholes.sandes.dk',
        'blackholes.wirehub.net', 'blacklist.sci.kun.nl',
        'bl.borderworlds.dk', 'bl.csma.biz', 'block.dnsbl.sorbs.net',
        'blocked.hilli.dk', 'blocklist2.squawk.com',
        'blocklist.squawk.com', 'bl.redhatgate.com',
        'bl.spamcannibal.org', 'bl.spamcop.net', 'bl.starloop.com',
        'bl.technovision.dk', 'cart00ney.surriel.com',
        'cbl.abuseat.org', 'dev.null.dk', 'dews.qmail.org',
        'dialup.blacklist.jippg.org', 'dialup.rbl.kropka.net',
        'dialups.mail-abuse.org', 'dialups.visi.com',
        'dnsbl-1.uceprotect.net', 'dnsbl-2.uceprotect.net',
        'dnsbl-3.uceprotect.net', 'dnsbl.antispam.or.id',
        'dnsbl.cyberlogic.net', 'dnsbl.njabl.org',
        'dnsbl.solid.net', 'dnsbl.sorbs.net', 'duinv.aupads.org',
        'dul.dnsbl.sorbs.net', 'dul.ru', 'dun.dnsrbl.net',
        'dynablock.wirehub.net', 'fl.chickenboner.biz',
        'forbidden.icm.edu.pl', 'hil.habeas.com',
        'http.dnsbl.sorbs.net', 'intruders.docs.uu.se',
        'korea.services.net', 'mail-abuse.blacklist.jippg.org',
        'map.spam-rbl.com', 'misc.dnsbl.sorbs.net',
        'msgid.bl.gweep.ca', 'multihop.dsbl.org',
        'no-more-funn.moensted.dk', 'orbs.dorkslayers.com',
        'orvedb.aupads.org', 'proxy.bl.gweep.ca', 'psbl.surriel.com',
        'pss.spambusters.org.ar', 'rblmap.tu-berlin.de',
        'rbl.schulte.org', 'rbl.snark.net', 'rbl.triumf.ca',
        'relays.bl.gweep.ca', 'relays.bl.kundenserver.de',
        'relays.dorkslayers.com', 'relays.mail-abuse.org',
        'relays.nether.net', 'rsbl.aupads.org', 'sbl.csma.biz',
        'sbl.spamhaus.org', 'sbl-xbl.spamhaus.org',
        'smtp.dnsbl.sorbs.net', 'socks.dnsbl.sorbs.net',
        'spam.dnsbl.sorbs.net', 'spam.dnsrbl.net',
        'spamguard.leadmon.net', 'spam.olsentech.net',
        'spamsources.dnsbl.info', 'spamsources.fabel.dk',
        'spamsources.yamta.org', 'spam.wytnij.to',
        'unconfirmed.dsbl.org', 'vbl.messagelabs.com',
        'web.dnsbl.sorbs.net', 'whois.rfc-ignorant.org',
        'will-spam-for-food.eu.org', 'xbl.spamhaus.org',
        'zombie.dnsbl.sorbs.net', 'ztl.dorkslayers.com',
        'cbl.abuseat.org', 'bhnc.njabl.org', 't1.dnsbl.net.au',
        'list.dsbl.org', 'luckyseven.dnsbl.net',
        'blacklist.spambag.org', 'dyna.spamrats.com',
        'spam.spamrats.com', 'ubl.unsubscore.com', 'db.wpbl.info',
        '0spam.fusionzero.com'
    );
    
    /**
     * Cached results per IP.
     * 
     * @var array
     */
    private $ips = array();

    
    /**
     * Has the data changed?
     * @var boolean
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
        
        // Try to retrieve the data from apc.
        $ips = $this->memcache()->get($this->MEMCACHE_KEY);
        
        if ($ips === false) {
            die('ouch');
            return;   
        }
        
        $this->ips = $ips;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        if (!$this->dataChanged) return;
        
        // store data in memcache for 24 hour.
        $retval = $this->memcache()->set($this->MEMCACHE_KEY, $this->ips, MEMCACHE_COMPRESSED, 86400);
        var_dump($retval);
    }
     
    /**
     * Check the given IP
     * 
     * @param string $ip
     * @return array of blacklists
     */
    public function getIP($ip)
    {
        if (isset($this->ips[$ip])) return $this->ips[$ip];

        // New result array
        $this->ips[$ip] = array();

        // Loop through all blacklists
        foreach ($this->blackslistdns as $blacklist) {
            // Check with this blacklist
            if ($this->checkIpWithBlacklist($ip, $blacklist)) {
                
                // Add this blacklist
                array_push($this->ips[$ip], $blacklist);
                
                // Update the changed variable
                if (!$this->dataChanged) $this->dataChanged = true;
            }
        }
        
        // return the array
        return $this->ips[$ip];
    }
    
    /**
     * Check the Ip against a single blacklist
     * 
     * @param string $ip
     * @param string $blacklist
     * @return boolean Blacklisted true/false
     */
    protected function checkIpWithBlacklist($ip, $blacklist)
    {
        // Dig the dns
        $output = exec('dig '.$this->reverseIp($ip).'.'.$blacklist);
        
        // Check the output
        return (preg_match_all('/127.0.0.([2-9]|10)/', $output) >= 1);
    }
    
    /**
     * Reverse the given ip address
     * 
     * @param string $ip
     * @return string
     */
    protected function reverseIp($ip)
    {
        return implode('.', array_reverse(explode('.', $ip)));   
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
}