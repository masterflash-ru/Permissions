<?php
/**
*плагин для контроллеров позволяет делать обращения в систему ACL и узнать разрешено то или иное действие
*/

namespace Mf\Permissions\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * 
 */
class Acl extends AbstractPlugin
{
    /**
    * экземпляр Mf\Permissions\Service\Acl
    */
    protected $AclService;
    
    /**
    * конфиг, только секция $config["permission"]["controllers"]
    */
    protected $config;
    
    /*полное имя класса контроллера*/
    protected $controller;
    
    /*имя метода контроллера*/
    protected $action;
    
public function __construct($AclService,$config) 
{
    $this->AclService = $AclService;
    $this->config=$config;
}

/*
*возвращает сам этот объект
*/    
public function __invoke()
{
    $matches = $this->getController()->getEvent()->getRouteMatch();
    $this->setController($matches->getParam('controller', null));
    $this->setAction($matches->getParam('action', null));
    return $this;
}


/*
* повторяет одноименный метод сервиса
*/
public function isAllowed($action = null)
{
    if (!isset($this->config[$this->controller][$this->action])){
        return false;
    }
    /*получить из конфига доступ к методу данного контроллера*/
    $p=$this->config[$this->controller][$this->action];
    return $this->AclService->isAllowed($action, $p);
}

/*
*установить имя контроллера
*/
public function setController($controller)
{
    $this->controller=$controller;
}

/*
*установить имя метода контроллера
*/
public function setAction($action)
{
    $this->action=$action;
}

/*
*получить имя контроллера
*/
public function getController()
{
    return $this->controller;
}

/*
*получить имя метода контроллера
*/
public function getAction()
{
    return $this->action;
}


/*
*получить сам сервис ACL
*/
public function GetAclService()
{
    return $this->AclService;
}



}