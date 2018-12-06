<?php
/**
*сервис контроля доступа к ресурсам по аналогии с UNIX
*/

namespace Mf\Permissions;


use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Adapter\Filesystem;



return [
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            Service\Acl::class => Service\Factory\AclFactory::class,
        ],
        'aliases'=>[
            "acl"=>Service\Acl::class,
            "Acl"=>Service\Acl::class,
        ],
    ],
    'controllers' => [
        'permission' => [],
    ],
    "permission"=>[
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
        ],
        'factories' => [
            Controller\Plugin\Acl::class => Controller\Plugin\AclFactory::class,
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
