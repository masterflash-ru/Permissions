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
    
    /*объект доступк к которому проверяем*/
    protected $resource;
    
    
public function __construct($AclService) 
{
    $this->AclService = $AclService;
}

/*
*возвращает сам этот объект
* $controller - полное имя объекта к которому проверяем доступ, по умолчанию текущий
* $action - имя метода контроллера к которому проверяем доступ, по умолчанию текущий
*/    
public function __invoke($resource=null)
{
    $this->resource=$resource;
    return $this;
}


/*
* повторяет одноименный метод сервиса
* $permission - строка запроса доступа - символ x r w d
* $resource - ресурс доступа, например, для объектов можно: ["объекта","имя_метода"] - по сути это путь
*  для простого объекта может быть просто строка
*/
public function isAllowed($permission,$resource=null)
{
    if (empty($resource)){
        $resource=$this->resource;
    }
    return $this->AclService->isAllowed($permission, $resource);
}

/*
*установить имя контроллера
*/
public function setResource($resource)
{
    $this->resource=$resource;
}

/*

/*
*получить имя контроллера
*/
public function getResource()
{
    return $this->resource;
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