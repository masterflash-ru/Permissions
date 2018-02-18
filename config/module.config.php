<?php
/**
*сервис контроля доступа к ресурсам посредством ролей
 */

namespace Mf\Permissions;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Authentication\AuthenticationService;

return [
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\RbacManager::class => Service\Factory\RbacManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
        ],
    ],
    
];
