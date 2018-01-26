<?php
/**
 */

namespace Mf\Permissions;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Session\SessionManager;	
use Admin\Service\AuthManager;
use Zend\EventManager\Event;

use Admin\Service\GetControllersInfo;

class Module
{

public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


public function onBootstrap(MvcEvent $event)
{
    
	$eventManager = $event->getApplication()->getEventManager();
    $sharedEventManager = $eventManager->getSharedManager();
    // объявление слушателя для проверки авторизации админа 
    $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);

	//слушатель для получения списка описания контроллеров, методов для виуазльного создания меню
	$sharedEventManager->attach("simba.admin", "GetControllersInfoAdmin", [$this, 'GetControllersInfoAdmin']);

}

/*слушатель для проверки авторизован ли админ*/
public function onDispatch(MvcEvent $event)
 {
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

	//  \Zend\Debug\Debug::dump(get_class_methods ($event->getApplication()->getMvcEvent()->getController() ));

	    if ($controllerName!="Admin\Controller\LoginController")
        {
       		$authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

            $result = $authManager->filterAccess($controllerName, $actionName);
           
           if ($result==AuthManager::AUTH_REQUIRED) {return $controller->redirect()->toRoute('admin');}
            	else if ($result==AuthManager::ACCESS_DENIED) {return $controller->redirect()->toRoute('admin403');}
	
			 //для данного модуля изменить макет
			if (false === strpos($controllerName, __NAMESPACE__)) { return; }
			$viewModel = $event->getViewModel();
			$viewModel->setTemplate('layout/admin_layout');		
		}   
}

/*
слушает событие GetControllersInfoAdmin 
для визуаллизации в админке маршрутов/путей в меню админки
в параметрах передается:
name=>имя_раздела "admin", ""
container - объект с интерфейсом Interop\Container\ContainerInterface - то что передается в фабрики
*/
public function GetControllersInfoAdmin(Event $event)
{
	$name=$event->getParam("name",NULL);
	$container=$event->getParam("container",NULL);
	
	//сервис который будет возвращать
	$service=$container->build("Admin\Service\GetControllersInfo",["name"=>$name]);
	return $service->GetDescriptors();
}

}
