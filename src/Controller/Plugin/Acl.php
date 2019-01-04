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
    * конфиг, только секция $config["permission"]["access_list"]
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
* $controller - полное имя контроллера к которому проверяем доступ, по умолчанию текущий
* $action - имя метода контроллера к которому проверяем доступ, по умолчанию текущий
*/    
public function __invoke($controller=null,$action=null)
{
    $matches = $this->getController()->getEvent()->getRouteMatch();
    if (empty($controller)){
        $this->setController($matches->getParam('controller', null));
    } else {
        $this->setController($controller);
    }
    if (empty($action)){
        $this->setAction($matches->getParam('action', null));
    } else {
        $this->setAction($action);
    }
    return $this;
}


/*
* повторяет одноименный метод сервиса
* $permission - строка запроса доступа - символ x r w d
* $controller - полное имя контроллера к которому проверяем доступ, по умолчанию текущий
* $action - имя метода контроллера к которому проверяем доступ, по умолчанию текущий
*/
public function isAllowed($permission,$controller=null,$action=null)
{
    if (empty($controller)){
        $controller=$this->controller;
    } 
    if (empty($action)){
        $action=$this->action;
    } 
    /*если нет в конфиге параметров доступа, тогда доступ запрещен*/
    if (!isset($this->config[$controller][$action])){
        return false;
    }
    /*получить из конфига доступ к методу данного контроллера*/
    return $this->AclService->isAllowed($permission, $this->config[$controller][$action]);
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
* этот сервис имеет более широкие возможности
*/
public function GetAclService()
{
    return $this->AclService;
}



}