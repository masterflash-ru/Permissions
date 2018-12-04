<?php
/**
*сервис контроля доступа к ресурсам по аналогии с UNIX
*/

namespace Mf\Permissions;

use Zend\Authentication\AuthenticationService;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Adapter\Filesystem;



return [
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            Service\Acl::class => Service\Factory\AclFactory::class,
            Service\User::class => Service\Factory\UserFactory::class,
        ],
        'aliases'=>[
            "acl"=>Service\Acl::class,
            "Acl"=>Service\Acl::class,
            "users"=>Service\UserManager::class,
            "Users"=>Service\UserManager::class,
        ],
    ],
    'controllers' => [
        'permission' => [],
    ],
    "permission"=>[
        /*список допустимых состояний регистрированных юзеров, ключ - это код состояния*/
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
        /*нормальное состояние посетителя, когда он может делать все*/
        "users_status_normal" => 3,
        
        /*корневой владелец и его разрешения
        * root,группа администраторов, 0744 (rwxr--r--)
        */
        "root_owner" =>[
            1,1,0744
        ],
    ],
    /*помощник в контроллеры для проверки доступа и для работы с авторизованным юзером*/
    'controller_plugins' => [
        'aliases' => [
            'acl' => Controller\Plugin\Acl::class,
            'Acl' => Controller\Plugin\Acl::class,
            'Zend\Mvc\Controller\Plugin\Acl' => Controller\Plugin\Acl::class,
            'user' => Controller\Plugin\User::class,
            'User' => Controller\Plugin\User::class,
            'Zend\Mvc\Controller\Plugin\User' => Controller\Plugin\User::class,
        ],
        'factories' => [
            Controller\Plugin\Acl::class => Controller\Plugin\AclFactory::class,
            Controller\Plugin\User::class => Controller\Plugin\UserFactory::class,
        ],
    ],
    // Настройка кэша.
    'caches' => [
        'DefaultSystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    'cache_dir' => './data/cache',
                    'ttl' => 60*60*2 
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                    ],
                ],
            ],
        ],
    ],

];
