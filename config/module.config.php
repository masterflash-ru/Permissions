<?php
/**
*сервис контроля доступа к ресурсам посредством ролей
 */

namespace Mf\Permissions;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Authentication\AuthenticationService;

return [
	//маршруты
    'router' => [
        'routes' => [

			//ошибка 403
            'admin40300000000' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin000000/403',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'e403',
                    ],
                ],
			],			

	    ],
    ],
	//контроллеры
    'controllers' => [
        'factories' => [
		
			//если мы используем нашу фабрику вызова, класс должен включать интерфейс FactoryInterface
			//Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,	
        ],
    	
		//если у контроллера нет коннструктора или он не нужен или пустой
        'invokables' => [
        ],
	],
	//плагины контроллеров, грубоговоря это дополнительные перегруженные методы внутри контроллера
    'controller_plugins' => [
		//фабрики плагинов
        'factories' => [
           Controller\Plugin\AccessPlugin::class => Controller\Plugin\Factory\AccessPluginFactory::class,
        ],

        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
        ],
    ],
	
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
			Service\RbacManager::class => Service\Factory\RbacManagerFactory::class,
        ],
    ],

 

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
	
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
	
];
