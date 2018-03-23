<?php
namespace Mf\Permissions\Service;

use Mf\Permissions\Entity\Users;

use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Exception;
use ADO\Service\RecordSet;
use ADO\Service\Command;


/**
 * сервис для управления юзерами, объект не привязан ни к какому юзеру в данный момент
 * получается что бы управлять юзерами сюда нужно передавать ID юзера с которым работаем
 * использует 2 таблицы users и users_ext - поля таблиц считываются в конструкторе и используются для автоматического распределения данных
 */
class UserManager
{
    /**
     * соединение с базой
     */
    protected $connection;
    
    /**
    * кеш
    */
    protected $cache;
    /*
    *массив имен колонок в базовой таблице юзеров
    */
    protected $db_field_base=[];

    /*
    *массив имен колонок в расширеной  таблице юзеров
    *имена полей зависит от приложения и их имена записываются в конструкторе
    *первичный ключ id считается железно
    */
    protected $db_field_ext=[];

    /**
     * Constructs the service.
     */
    public function __construct($connection,$cache) 
    {
        $this->connection = $connection;
        $this->cache=$cache;
        
        $key="users_tables_structure";
        //пытаемся считать из кеша
        $result = false;
        $users_tables_structure= $this->cache->getItem($key, $result);
        if (!$result) {
            //промах кеша, создаем
            $rs=new RecordSet();
            $rs->Open("show columns from users",$this->connection);
            while (!$rs->EOF){
                $users_tables_structure[0][]=$rs->Fields->Item["Field"]->Value;
                $rs->MoveNext();
            }
            $rs->Close();
            $rs=new RecordSet();
            $rs->Open("show columns from users_ext",$this->connection);
            while (!$rs->EOF){
                $users_tables_structure[1][]=$rs->Fields->Item["Field"]->Value;
                $rs->MoveNext();
            }
            $rs->Close();

            //сохраним в кеш
            $this->cache->setItem($key, $users_tables_structure);
        }
        $this->db_field_base=$users_tables_structure[0];
        $this->db_field_ext=$users_tables_structure[1];

    }
    
    /**
     * добавить нового юзера
     *на входе массив ключи которого это имена колонок
     *в какую таблицу писать работает автоматически
     *возвращается экземпляр Mf\Permissions\Entity\Users с заполнеными данными
     */
    public function addUser($data) 
    {
        if(empty($data['login'])) {
            throw new Exception("Нет обязательного параметра login, добавить нового юзера нельзя");
        }
        if($this->isUserExists($data['login'])) {
            throw new Exception("Пользователь с логином " . $data['login'] . " уже зарегистрирован");
        }
        return $this->_updateUserInfo(0, $data,true);
    }
    
    /*
    *получить инфу по юзеру c id
    *возвращает users  (экземпляр объекта)
    */
    public function GetUserIdInfo($id)
    {
        $id=(int)$id;
        $key="users_{$id}";
        //пытаемся считать из кеша
        $result = false;
        $user= $this->cache->getItem($key, $result);
        if (!$result) {
            //читаем и заполняем сущность "юзер"
            $this->connection->BeginTrans();
            $rs=$this->connection->Execute("select * from users u,users_ext e where u.id=e.id and u.id=".(int)$id);
            $this->connection->CommitTrans();

            if ($rs->EOF){
                throw new \Exception("Юзера с id={$id} не существует");
            }
            $user=$rs->FetchEntity(Users::class);
            $rs->Close();
            //сохраним в кеш
            $this->cache->setItem($key, $user);
        }
        return $user;
    }

    /**
     * Обновление инфы в профиле юзера, автоматом пишется в основную или дополнительную таблицы.
     * $userid = ID юзера длья которог оменяем инфу
     * если ошибка - исключение
     * возвращает экземпляр users
     */
    public function updateUserInfo ($userid, $data) 
    {
        $userid=(int)$userid;
        $this->cache->removeItem("users_{$userid}");
        return $this->_updateUserInfo($userid, $data);
    }
    
    
    /**
     * проверяет наличие юзера с указаным логином в базе.     
     *возвращает true - есть в базе, false - нет
     */
    public function isUserExists($login) 
    {
        $c=new Command();
        $c->NamedParameters=true;
        $c->ActiveConnection=$this->connection;
        $p=$c->CreateParameter('login', adChar, adParamInput, 127, $login);//генерируем объек параметров
        $c->Parameters->Append($p);//добавим в коллекцию
        $c->CommandText="select id from users where login=:login";

        $rs=new RecordSet();
        $rs->Open($c);

        return !$rs->EOF;
    }
    
    /**
    * получить массив ID групп, которым принадлежит юзер с id
    * user_id - ID юзера
    * возвращается массив всегда, если юзер не найден - исключение
    */
    public function getGroupIds($user_id)
    {
        $user_id=(int)$user_id;
        $key="group_users_{$user_id}";
        //пытаемся считать из кеша
        $result = false;
        $group= $this->cache->getItem($key, $result);
        if (!$result) {
            //читаем и заполняем сущность "юзер"
            $this->connection->BeginTrans();
            $rs=$this->connection->Execute("select users_group from users2group  where users={$user_id}
                                            union
                                            select ugt.parent_id as users_group from users_group_tree as ugt,users2group ug 
                                                where ugt.id=ug.users_group and ug.users={$user_id}");
            $this->connection->CommitTrans();
            $group=[];
            while (!$rs->EOF){
                $group[]=(int)$rs->Fields->Item["users_group"]->Value;
                $rs->MoveNext();
            }
            $group=array_unique($group);
            
            $rs->Close();
            //сохраним в кеш
            $this->cache->setItem($key, $group);
        }
        return $group;
    
    }

    /**
    *привязать юзера к группам, старые связи будут удалены
    * $user_id - int - ID юзера
    * $group_ids - массив ID групп, к которым привяжется юзер
    */
    public function setGroupIds($user_id,array $group_ids)
    {
        $user_id=(int)$user_id;
        //удалим старые связи
        $this->connection->Execute("delete from users2group where users={$user_id}");
        $rs1=new RecordSet();
        $rs1->CursorType = adOpenKeyset;
        $rs1->open("SELECT * FROM users2group where users={$user_id}",$this->connection);
        
        foreach ($group_ids as $gr){
            $rs1->AddNew();
            $rs1->Fields->Item['users']->Value=$user_id;
            $rs1->Fields->Item['users_group']->Value=$gr;
            $rs1->Update();
        }
        $this->cache->removeItem("group_users_{$user_id}");
    }
    
    /**
     * генерация уникальной случайной строки для генерации адреса 
     * просто возвращает уникальную строку - это и есть новый пароль
     * 
     */
    public function generatePasswordReset($passwordLen=10)
    {
        //генерируем временный пароль и дату его годности
        return Rand::getString($passwordLen, '0123456789!@#%$abcdefghijklmnopqrstuvwxyz', true);
    }
    
    /**
     * Checks whether the given password reset token is a valid one.
     * /
    public function validatePasswordResetToken($passwordResetToken)
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if($user==null) {
            return false;
        }
        
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);
        
        $currentDate = strtotime('now');
        
        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }
        
        return true;
    }
    
    /**
     * This method sets new password by password reset token.
     * /
    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
           return false; 
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if ($user==null) {
            return false;
        }
                
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);        
        $user->setPassword($passwordHash);
                
        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);
        
        $this->entityManager->flush();
        
        return true;
    }
    
    /**
     * Обновление инфы/создание в профиле юзера, автоматом пишется в основную или дополнительную таблицы.
     * $userid = ID юзера длья которог оменяем инфу
     * $flag_create_new - если true, если нет юзера, создается новая запись
     * если ошибка - исключение
     * возвращает экземпляр users
     */
    protected function _updateUserInfo ($userid=0, array $data=[],$flag_create_new=false) 
    {

        $userid=(int)$userid;
        $rs=new RecordSet();
        $rs->CursorType =adOpenKeyset;
        $rs->Open("select * from users where id=$userid",$this->connection);
        if($rs->EOF && !$flag_create_new) {
            throw new \Exception("Юзера с id={$userid} не найдено");
        }
        if($rs->EOF && $flag_create_new) {
            $rs->AddNew();
        }

        $this->connection->BeginTrans();
        
        if (isset($data['password'])){
            // шифруем пароль
            $bcrypt = new Bcrypt();
            $data['password'] = $bcrypt->create($data['password']);
        }
        if (isset($data['temp_password'])){
            // шифруем пароль
            $bcrypt = new Bcrypt();
            $data['temp_password'] = $bcrypt->create($data['temp_password']);
        }
        
        //пробежим по базовой таблице
        foreach ($this->db_field_base as $field){
            if (array_key_exists($field,$data)){
                $rs->Fields->Item[$field]->Value=$data[$field];
            }
        }
        //запишем в базовую таблицу информацию и получим ID нового юзера
        $rs->Update();
        $this->connection->CommitTrans();
        
        
        $this->connection->BeginTrans();
        $rs_ext=new RecordSet();
        $rs_ext->CursorType =adOpenKeyset;
        $rs_ext->Open("select * from users_ext where id=$userid",$this->connection);
        if($rs_ext->EOF && $flag_create_new) {
            $rs_ext->AddNew();
            $userid=(int)$rs->Fields->Item["id"]->Value;
            $data["id"]=$userid;

        }

        //пробежим по расширеной таблице
        foreach ($this->db_field_ext as $field){
            if (array_key_exists($field,$data)){
                $rs_ext->Fields->Item[$field]->Value=$data[$field];
            }
        }
        $rs_ext->Update();
        $this->connection->CommitTrans();
        $rs_ext->Close();
        $rs->Close();
        return $this->GetUserIdInfo($userid);
    }

}

