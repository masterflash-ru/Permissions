<?php
/**
*сервис контроля доступа к ресурсам по аналогии с UNIX
 */

namespace Mf\Permissions;

use Zend\Authentication\AuthenticationService;

return [
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
        ],
    ],
    "permission"=>[
        /*список допустимых состояний, ключ - это код состояния*/
        'users_status' => [
            0=>"Неопределенное",
            1=>"Неподтвержденная регистрация",
            2=>"Ожидает подтверждения администрации",
            3=>"Нормальное состояние",
            4=>"Заблокирован",
        ],
        /*код состояния при начальной регистрации нового посетителя*/
        "users_status_start_registration" => 1,
        /*новый код состояния после подтверждения регистрации*/
        "users_status_after_confirm" => 3,
    ]
    
];
