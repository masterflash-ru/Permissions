<?php
/**
*собственно сервис проверки доступа юзера по входным данным:
* ID юзера, ID его группы, битовая структура доступа, битовая структура родителя
*для совестимости использует интерфейсы zend-permissions-acl - остальное не используется
*логика работы совсем отличается! интерфейсы исключительно для совместимости с другими пакетами, например, навигации
*/

namespace Mf\Permissions\Service;

use Zend\Permissions\Acl\AclInterface;
use Exception;

class Acl implements AclInterface
{
    protected $connection;
    protected static $root_owner;    /*доступы которые дает главный владелец (root) по умолчанию*/
    protected $r_action=[256,32,4];

public function __construct($connection,$config) 
{
    $this->connection=$connection;
    if (!empty($config["permission"]["root_owner"]) && is_array($config["permission"]["root_owner"]) && count($config["permission"]["root_owner"])==3){
        self::$root_owner=$config["permission"]["root_owner"];
    } else {
        self::$root_owner=[1,1,0444];
    }
}

    
/*
* метод не используется, всегда возвращается true
*/
public function hasResource($resource) 
{
    return true;
}
    
    
    
/* метод проверяет разрешено то или иное действие (true или false), передается варианты действий, аналогичных в UNIX, это r, w, x
* $action - строка действия: r (чтение), w (запись), x (исполнение/поиск)
* $permission - срока вида "ID_юзера,ID_его_группы,код_доступа", например, 
*       "1,1,0777" - юзер 1, группа 1, код доступа 0777 (в восмеричном виде, можно любое число)
* можно передать все это в виде массива, то же пример: [1,1,0777]
* $parent_permission - точно такая же структура в которой информация о родительских доступах
* если она не задана, берется root_owner из конфига - это корневой владелец и его доступы
*/    
public function isAllowed($action = null, $permission = null, $parent_permission = null)
{
    if (empty($parent_permission)) {
        $parent_permission=self::$root_owner;
    }
    if (!is_string($action) || !in_array($action,["r","w","x"])) {
        throw new Exception("Неверный 1-й параметр в isAllowed, должен быть символ: r,w,x");
    }
    $permission=$this->NormalizePermission($permission);
    $parent_permission=$this->NormalizePermission($parent_permission);
}

    
/*
*проверяет верность параметров доступа (сама структура)
*$permission - либо массив с 3-мя элементами, либо строка "ID_юзера,ID_его_группы,код_доступа"
*возвращает массив ["ID_юзера,ID_его_группы,код_доступа"]
*/
protected function NormalizePermission($permission=null)
{
    if (is_string($permission)) {
        $_permission=explode(",",$permission);
    } 
    if(is_array($permission)){
        if (count($permission)==3) {$_permission=$permission;}
    } else {$_permission=null;}
    
    if (empty($_permission)  ) {
        throw new Exception("Неверный параметр с юзером,группой,кодом (2 или 3 - параметры) в isAllowed, должен быть структура (строка) 'ID_юзера,ID_его_группы,код_доступа' или массив");
    }
return $_permission;
}
}
