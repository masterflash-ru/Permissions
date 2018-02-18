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
    public function __construct($connection) 
    {
        $this->connection = $connection;
        $rs=new RecordSet();
        $rs->Open("show columns from users",$this->connection);
        while (!$rs->EOF){
            $this->db_field_base[]=$rs->Fields->Item["Field"]->Value;
            $rs->MoveNext();
        }
        $rs->Close();
        $rs=new RecordSet();
        $rs->Open("show columns from users_ext",$this->connection);
        while (!$rs->EOF){
            $this->db_field_ext[]=$rs->Fields->Item["Field"]->Value;
            $rs->MoveNext();
        }
        $rs->Close();

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
        if(empty($data['password'])) {
            throw new Exception("Нет обязательного параметра password, добавить нового юзера нельзя");
        }

        if($this->isUserExists($data['login'])) {
            throw new Exception("Пользователь с логином " . $data['login'] . " уже зарегистрирован");
        }
        return $this->_updateUserInfo(0, $data,true);
    }
    
    /*
    *получить инфу по юзеру c id
    *возвращает users  
    */
    public function GetUserIdInfo($id)
    {
        //читаем и заполняем сущность "юзер"
        $this->connection->BeginTrans();
        $rs=$this->connection->Execute("select * from users u,users_ext e where u.id=e.id and u.id=".(int)$id);
        $this->connection->CommitTrans();

        if ($rs->EOF){
            throw new \Exception("Юзера с id={$id} не существует");
        }
        $user=$rs->FetchEntity(Users::class);
        $rs->Close();
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
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     * /
    public function generatePasswordResetToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->setPasswordResetToken($token);
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);  
        
        $this->entityManager->flush();
        
        $subject = 'Password Reset';
            
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = $_SERVER['REQUEST_SCHEME'] . $httpHost . '/set-password?token=' . $token;
        
        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";
        
        // Send email to user.
        mail($user->getEmail(), $subject, $body);
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
            if (!empty($data['id'])){
                /*удалим сохраненные данные если есть смена пароля и у нас обновление существующего юзера*/
                 $this->connection->Execute("delete from users_save_me where users=".(int)$data['id']);
            }
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

