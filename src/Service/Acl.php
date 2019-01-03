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

    protected $UserService;           /*экземпляр сервиса UserService*/
    protected static $root_owner;    /*доступы которые дает главный владелец (root) по умолчанию*/
    protected static $actions=[
                    "r"=>[256,32,4],
                    "w"=>[128,16,2],
                    "x"=>[64,8,1]
                ];
    protected $UserId;
    protected $GroupId;
    protected $GuestUser;
    protected $GuestGroup;

public function __construct($config,$UserService) 
{

    $this->UserService=$UserService;
    if (!empty($config["root_owner"]) && is_array($config["root_owner"]) && count($config["root_owner"])==3){
        self::$root_owner=$config["root_owner"];
    } else {
        self::$root_owner=[1,1,0444];
    }
    /*гостевые записи*/
    $this->GuestUser=$config["guest"][0];
    $this->GuestGroup=$config["guest"][1];
}

    
/*
* метод не используется, всегда возвращается true
*/
public function hasResource($resource) 
{
    return true;
}
    
    
    
/* метод проверяет разрешено то или иное действие (true или false), передается варианты действий, аналогичных в UNIX, это r, w, x, d
* $action - строка действия: r (чтение), w (запись), x (исполнение/поиск), d удаление
* $permission - срока вида "ID_юзера,ID_его_группы,код_доступа", например, 
*       "1,1,0777" - юзер 1, группа 1, код доступа 0777 (в восмеричном виде, можно любое число)
* можно передать все это в виде массива, то же пример: [1,1,0777]
* $parent_permission - точно такая же структура в которой информация о родительских доступах
* если она не задана, берется root_owner из конфига - это корневой владелец и его доступы

ВНИМАНИЕ! если никакой юзер не авторизован, и вызывается этот метод для проверки доступа, подразумевается, что защел гость и все проверяется с 
гостевой записью, см. конфиг, эти записи есть в базе
*/    
public function isAllowed($action = null, $permission = null, $parent_permission = null)
{
    if (empty($parent_permission)) {
        $parent_permission=self::$root_owner;
    }
    if (!is_string($action) || !in_array($action,["r","w","x","d"])) {
        throw new Exception("Неверный 1-й параметр в isAllowed, должен быть символ: r,w,x,d");
    }
    $permission=$this->NormalizePermission($permission);
    $parent_permission=$this->NormalizePermission($parent_permission);
    
    $user=$this->getUserId();
    $group=$this->getGroupIds();
    
    /*если никто не авторизовался, и идет проверка на доступ, то подставляем гостевые записи, они есть в базе и совпадают 
    *с конфигом пакета
    */
    if (empty($user)){
        $user=$this->GuestUser;
    }
    if (empty($group)){
        $group=[$this->GuestGroup];
    }
    

    /*если юзера нет, или он не связан с группой - это ошибка скорей всего, поэтому доступ закрыт*/
    if (empty($user) || empty($group)) {return false;}
    
    /*для root и группы Администраторов всегда все разрешено*/
    if ($user==1 || in_array((int)$parent_permission[1], $group)){
        return true;
    }
    
    switch ($action){
        case "r":
        case "w":
        case "x":{
            /*смотрим разрешение для владельца*/
            if ($user==(int)$parent_permission[0]){
                if (self::$actions[$action][0] & $permission[2]) {return true;}
            }

            /*смотрим для группы*/
            if (in_array((int)$parent_permission[1], $group)){
                if (self::$actions[$action][1] & $permission[2]) {return true;}
            }
            /*юзер не принадлежит ни к кому, смотрим разрешения "для всех"*/
            if (self::$actions[$action][2] & $permission[2]) {return true;}

            break;
        }
        
        case "d":{
            /*удаление смотрим по особому, нужно учитывать бит Sticky (512)
            * для владельца все как обычно, если разрешен w, значит можно удалить
            * для остальных, проверяем, если этот бит не установлен
            */
            if ($user==(int)$parent_permission[0]){
                if (self::$actions[$action][0] & $permission[2]) {return true;}
            }
            if (!$permission[2] & 512){
                /*если не установлен Sticky (512) - тогда смотрим остальным разрешение*/
                /*смотрим для группы*/
                if (in_array((int)$parent_permission[1], $group)){
                    if (self::$actions[$action][1] & $permission[2]) {return true;}
                }
                /*юзер не принадлежит ни к кому, смотрим разрешения "для всех"*/
                if (self::$actions[$action][2] & $permission[2]) {return true;}

            }
            break;
        }
    }
    /*доступ запрещен*/
    return false;
}

    
/**
* внутренняя, возвращает ID авторизованного юзера
* и сохраняет в этом объекте
*/
protected function getUserId()
{
    if (empty($this->UserId)){
        $this->UserId=$this->UserService->getUserId();
    }
    return $this->UserId;
}

/**
* внутренняя возвращает массив ID групп, которым принадлежит авторизованный юзер
*/
protected function getGroupIds()
{
    if (empty($this->GroupId)){
        $this->GroupId=$this->UserService->getGroupIds();
    }
    return $this->GroupId;

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
