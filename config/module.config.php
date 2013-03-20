<?php
return array(

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller'    => 'BalIpReputation\Controller\Reputation',
                        'action'        => 'index'
                    )
                )
            )
        )
    ),
    
    'service_manager' => array(
        'factories' => array(
            'BalIpReputation\Options\ModuleOptions' => 'BalIpReputation\Options\ModuleOptionsFactory',
            'BalIpReputation\Service\Blacklists'    => 'BalIpReputation\Service\BlacklistsFactory',
            'BalIpReputation\Service\Senderbase'    => 'BalIpReputation\Service\SenderbaseFactory',
            'BalIpReputation\Service\Honeypot'      => 'BalIpReputation\Service\HoneypotFactory',
        ),
    ),
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'layout/layout'                 => __DIR__ . '/../view/layout/layout.phtml',
            'bal-ip-reputation/index/index' => __DIR__ . '/../view/bal-ip-reputation/index/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ), 
    
    'bal_ip_reputation' => array(
        'ipAddresses' => array(
            '87.250.145.74'
        ),
    ),
);
