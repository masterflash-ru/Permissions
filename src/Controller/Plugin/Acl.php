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
    * весь конфиг ????
    */
    protected $config;
    
    /**
    * массив доступов к контроллерам из конфига
    */
    protected $ControllerPermissions=[];
    
    
public function __construct($AclService,$config) 
{
    $this->AclService = $AclService;
    $this->config=$config;
    $this->ControllerPermissions=$config['controllers']['permission'];
}
/*
*повторяет сервис Acl, ему передается управление при обращениях сюда
*__invoke() - с параметрами это точная копия isAllowed сервиса Acl (не путать с Zend-овским ACL!!!)
* если все параметры равны null - возвращается экземпляр этого объекта 
*
* метод проверяет разрешено то или иное действие, передается варианты действий, аналогичных в UNIX, это r, w, x, d
* $action - строка действия: r (чтение), w (запись), x (исполнение/поиск)
* $permission - срока вида "ID_юзера,ID_его_группы,код_доступа", например, 
*       "1,1,0777" - юзер 1, группа 1, код доступа 0777 (в восмеричном виде, можно любое число)
* можно передать все это в виде массива, то же пример: [1,1,0777]
* $parent_permission - точно такая же структура в которой информация о родительских доступах
*/    
public function __invoke($action = null, $permission = null, $parent_permission = null)
{
    /*если все пусто вернем сам этот объект*/
   if (empty($action) && empty($permission) && empty($parent_permission)){
       return $this;
   }
}

/**
* проверяет разрешение для указанного контроллера 
* $controller - строка имени контроллера, например, Admin\Controller\IndexController, если null, то берется текущий
* $action - строка имени действия увказанного контроллера, например, index (Action как в методе опускается), если null, то текущее значение
* возвращает true или false , доступ разрешен/запрещен
*
* по сути проверяется параметр x в доступе, маска доступа описана в конфиге в секции permission в контроллерах
*/
public function isAllowedControllerAction($controller = null, $action = null)
{
    if (empty($controller)) {
        $controller=get_class($this->getController());
    }
    if (empty($action)) {
        $action=$this->getController()->params()->fromRoute('action', null);
    }
    /*не прописано ничего, если проверяем на доступ, то доступ запрещатся*/
    if (isset($this->ControllerPermissions[$controller][$action])) {
        /*непосредственно проверяем, родительский доступ это значение root_owner из конфига*/
        return $this->AclService->isAllowed("x", $this->ControllerPermissions[$controller][$action], null);
    }
    return false;
}

/*
* повторяет одноименный метод сервиса
*/
public function isAllowed($action = null, $permission = null, $parent_permission = null)
{
    return $this->AclService->isAllowed($action, $permission, $parent_permission);
}


/*
*получить сам сервис ACL
*/
public function GetAclService()
{
    return $this->AclService;
}



}