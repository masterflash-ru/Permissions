<?php
/**
*сервис контроля доступа к ресурсам посредством ролей
 */

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;


return [
	//маршруты
    'router' => [
        'routes' => [

			//ошибка 403
            'admin403' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/403',
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
			Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,	
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
        ],
    ],

   
   
        /* Determine mode - 'restrictive' (default) or 'permissive'. 
		всем, если мы поставим звездочку (*);
		любому аутентифицированному пользователю, если мы поставим коммерческое at (@);
		конкретному аутентифицированному пользователю с заданным адресом эл. почты личности, если мы поставим (@identity)
		любому аутентифицированному пользователю с заданной привилегией, если мы поставим знак плюса и имя привилегии (+permission).
*/

    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed 
            // under the 'access_filter' config key, and access is denied to any not listed 
            // action for not logged in users. In permissive mode, if an action is not listed 
            // under the 'access_filter' key, access to it is permitted to anyone (even for 
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],

        'controllers' => [
            Controller\IndexController::class => [
                //разрешение для входа
                ['actions' => '*', 'allow' => '+admin.login'],
            ],
            Controller\ConstructorLineController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\ConstructorTreeController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
	        Controller\TreeController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\BackupRestoreController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\LineController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\CkeditorController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\EntityController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
			
        ]
    ],


    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
	
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
	
];
