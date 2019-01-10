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
    
    /*объект доступк к которому проверяем*/
    protected $resource;
    
    
public function __construct($AclService) 
{
    $this->AclService = $AclService;
}

/*
*возвращает сам этот объект
$resource - ресурс доступа, например, для объекта: ["имя_объекта","имя_метода"] - по сути это путь
*  для простого объекта может быть просто строка
*/    
public function __invoke($resource=null)
{
    $this->resource=$resource;
    return $this;
}


/*
* повторяет одноименный метод сервиса
* $permission - строка запроса доступа - символ x r w d
* $resource - ресурс доступа, например, для объекта: ["имя_объекта","имя_метода"] - по сути это путь
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