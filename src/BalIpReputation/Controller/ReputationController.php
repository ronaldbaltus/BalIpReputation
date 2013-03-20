<?php
namespace BalIpReputation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ReputationController extends AbstractActionController
{
    public function indexAction()
    {
        $options = $this->getServiceLocator()->get('BalIpReputation\Options\ModuleOptions');
        
        return new ViewModel(array(
            'ipAddresses'   => $options->getIpAddresses(),
            'blacklists'    => $this->getServiceLocator()->get('BalIpReputation\Service\Blacklists'),
            'senderbase'    => $this->getServiceLocator()->get('BalIpReputation\Service\Senderbase'),
            'honeypot'      => $this->getServiceLocator()->get('BalIpReputation\Service\Honeypot'),
        ));
    }
}
  
?>
