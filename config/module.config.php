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
    /*конфиг доступов к разым объектам системы по умолчанию, все пусто*/
    "permission"=>[
        /*корневой владелец и доступ по умолчанию*/
        "root_owner" =>[1,1,0744],
        "guest_owner" =>[2,2,0666],
    ],
    /*помощник в контроллеры для проверки доступа и для работы с авторизованным юзером*/
    'controller_plugins' => [
        'aliases' => [
            'acl' => Controller\Plugin\Acl::class,
            'Acl' => Controller\Plugin\Acl::class,
        ],
        'factories' => [
            Controller\Plugin\Acl::class => Controller\Plugin\AclFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\Acl::class => View\Helper\AclFactory::class,
        ],
        'aliases' => [
            'acl' => View\Helper\Acl::class,
            'Acl' => View\Helper\Acl::class,
        ],
    ],

    /*описатели интерфейсов*/
    "interface"=>[
        "permissions"=>__DIR__."/admin.permissions.php",
    ]

];
