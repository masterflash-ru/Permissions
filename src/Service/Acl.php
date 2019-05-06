<?php
/**
*собственно сервис проверки доступа юзера по входным данным:
* ID юзера, ID его группы, битовая структура доступа, битовая структура родителя
*для совестимости использует интерфейсы zend-permissions-acl - остальное не используется
*логика работы совсем отличается! интерфейсы исключительно для совместимости с другими пакетами, например, навигации
*/

namespace Mf\Permissions\Service;


use Exception;

class Acl
{

    protected $UserService;           /*экземпляр сервиса UserService*/
    protected static $root_owner;    /*доступы которые дает главный владелец (root) по умолчанию*/
    protected static $guest_owner;    /*доступы которые дает гость по умолчанию*/
    protected static $permissions=[]; /*массив доступов, считанных из базы*/
    protected static $actions=[
                    "r"=>[256,32,4],
                    "w"=>[128,16,2],
                    "x"=>[64,8,1]
                ];
    protected $UserId;
    protected $GroupId;
    protected $connection;
    protected $config;
    protected $cache;

public function __construct($connection,$UserService,$cache,$config) 
{

    $this->UserService=$UserService;
    $this->config=$config;
    $this->cache=$cache;
    
    $this->connection=$connection;

    /*читаем доступы из таблицы и сохраним в кеш*/
    $key="permissions";
    //пытаемся считать из кеша
    $result = false;
    static::$permissions= $this->cache->getItem($key, $result);
    if (!$result){
        $rs=$connection->Execute("select object,mode,owner_user,owner_group from permissions");
        while(!$rs->EOF){
            $permissions[$rs->Fields->Item["object"]->Value]=[
                $rs->Fields->Item["owner_user"]->Value,
                $rs->Fields->Item["owner_group"]->Value,
                $rs->Fields->Item["mode"]->Value,
                ];
            $rs->MoveNext();
        }
        /*заменим метасимолы подмены*/
        foreach ($permissions as $k=>$v){
            if (false!==strpos($k,"*")){
                $k=preg_quote($k);
                $k_new=str_replace("\*",'[a-zA-Z0-9_]+',$k) ;
                unset($permissions[$k]);
                $permissions[$k_new]=$v;
            }
        }
        static::$permissions=$permissions;
        //сохраним в кеш
        $this->cache->setItem($key, static::$permissions);
    }
    /*на всякий случай, если таблица пустая, сделать пустой массив*/
    if (!is_array(static::$permissions)){
        static::$permissions=[];
    }
    static::$root_owner=$config["permission"]["root_owner"];
    static::$guest_owner=$config["permission"]["guest_owner"];//
}

    
/*
* метод не используется, всегда возвращается true
*/
public function hasResource($resource) 
{
    return true;
}

/*
* $action - строка запроса доступа - символы:
* x - исполнение,
* r - чтение,
* w - запись,
* d - удаление
* p - изменение прав доступа (может только root или владелец ресурса)
* $resource - ресурс доступа, строка, например, Application\Controller\IndexController/index - по сути это путь
*  или Application\Controller\IndexController::index
* .          допускается массив элементов
*  для простого объекта может быть просто строка
*/
public function isAllowed($action = null,$resource=null)
{
    //для root доступ всегда разрешен
    if ($this->getUserId()==1){return true;}
    
    if (is_array($resource)){
        $resource=implode("/",$resource);
    }
    $p=$this->searchResource($resource);
    if (count($p)!=2){return false;}
    
    if ($action=="p"){//проверка возможности смены доступа (владелец и root)
        return ($this->getUserId()==$p[0][0]);
    }

    return $this->checkAcl($action, $p[0],$p[1]);
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
public function checkAcl($action = null, $permission = null, $parent_permission = null)
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
        $user=static::$guest_owner[0];
    }
    if (empty($group)){
        $group=[static::$guest_owner[1]];
    }
    

    /*если юзера нет, или он не связан с группой - это ошибка скорей всего, поэтому доступ закрыт*/
    if (empty($user) || empty($group)) {return false;}

    /*для root и группы Администраторов всегда все разрешено*/
    if ($user==1 /*|| in_array((int)$parent_permission[1], $group)*/){
        return true;
    }
   
    switch ($action){
        case "r":
        case "w":
        case "x":{
            /*смотрим разрешение для владельца*/
            if ($user==(int)$permission[0] && self::$actions[$action][0] & $permission[2]){return true;}

            /*смотрим для группы*/
            if (in_array((int)$permission[1], $group) && self::$actions[$action][1] & $permission[2]){return true;}
            
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
* поиск объекта в таблице
* resource - строка имени ресурса, представляет собой:
* Namespace\Object/function или  Namespace\Object::function
* если все методы/функции имеют один доступ, то можно использовать Namespace\Object/* или  Namespace\Object::*
* возвращает массив из 2-х элементов:[массив_доступов_объекта,массив_доступов_родителя]
* если родитель не найден, тогда родителем считается root
*/
protected function searchResource(string $resource)
{
    $resource=str_replace("::","\\",$resource);
    $r=array_filter(static::$permissions,function($k) use ($resource) {
        /*прямое совпадение*/
        if ($k===$resource){return true;}
        /*возможный родитель   пока не реализовано*/
        if ($k=== preg_replace("/\/[a-z0-9]*$/ui","",$resource) ){return true;}
        /*метасимволы, если есть, обрабатываем как регулярные выражения*/
        if (false!==strpos($k,"[")){
            return (boolean)preg_match("#{$k}#ui",$resource);
        }
        return false;
    },ARRAY_FILTER_USE_KEY);

    /*порядок (доступов):
    * [искомый объект,родительский]
    */
    uksort($r,"strcmp");
    $r=array_reverse($r);
    /*если в массиве одно значение, то это может быть родительский объект, проверим
    * случай, если это так, если не совпадает имя, тогда удалим его, т.к. искомый объект не найден
    * ПОКА НЕ РАБОТАЕТ
    if (count($r)==1 && key($r)!=$resource){
        unset($r[key($r)]);
    }*/
    /*меняем ключи на числовые*/
    $rez=[];
    foreach ($r as $v){
        $rez[]=$v;
    }
    if (count($rez)==1){
        $rez[]=static::$root_owner;
    }

    return $rez;
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
