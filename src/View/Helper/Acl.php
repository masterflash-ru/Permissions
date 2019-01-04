<?php
/*

*/

namespace Mf\Permissions\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Exception;

class Acl extends AbstractHelper 
{
    /**
    * экземпляр Mf\Permissions\Service\Acl
    */
    protected $AclService;
    
    /**
    * конфиг, только секция $config["permission"]["access_list"]
    */
    protected $config;
    
    /*полное имя класса помощника*/
    protected $view_helper;
    
    
public function __construct($AclService,$config) 
{
    $this->AclService = $AclService;
    $this->config=$config;
}

/*
*возвращает сам этот объект
*/    
public function __invoke($view_helper=null)
{
    $this->setViewHelperName($view_helper);
    return $this;
}
/*
* повторяет одноименный метод сервиса
*/
public function isAllowed($action = null,$view_helper=null)
{
    $this->setViewHelperName($view_helper);
    if (!isset($this->config[$this->view_helper])){
        return false;
    }
    /*получить из конфига доступ к методу данного контроллера*/
    $p=$this->config[$this->view_helper];
    return $this->AclService->isAllowed($action, $p);
}

    
public function setViewHelperName($view_helper)
{
    if (!is_null($view_helper)){
        if (is_object($view_helper)){
            $view_helper=get_class($view_helper);
        }
        $this->view_helper=$view_helper;
    } else {
        throw new Exception ("Не указан экземпляр ");
    }

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