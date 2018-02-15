<?php
namespace Mf\Permissions\Service;

use Mf\Permissions\Entity\User;

use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Exception;

/**
 * сервис для управления юзерами
 * 
 */
class UserManager
{
    /**
     * соединение с базой
     */
    protected $connection;
    
    /*
    *массив имен колонок в базовой таблице юзеров
    *при изменении основной таблицы обязательно здесь должно быть отражено!
    *первичный ключ id считается железно
    */
    protected $db_field_base=[
        "login","status","password","name","full_name",
        "temp_password","temp_date","confirm_hash",
        "date_registration","date_last_login",
    ];

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
        $rs->Open("select * from users_ext limit 1",$this->connection);
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            $this->db_field_ext[]=$column_name;
        }
    }
    
    /**
     * добавить нового юзера
     *на входе массив ключи которого это имена колонок
     *в какую таблицу писать работает автоматически
     *возвращается экземпляр Mf\Permissions\Entity\User с заполнеными данными
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
        
        $rs=new RecordSet();
        $rs->Open("select * from users limit 1",$this->connection);
        $rs->AddNew();
        $this->connection->BeginTrans();
        // шифруем пароль
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);
        
        //пробежим по базовой таблице
        foreach ($this->db_field_base as $field){
            if (in_array($field,$data)){
                $rs->Fields->Item[$field]->Value=$data[$field];
            }
        }
        //запишем в базовую таблицу информацию и получим ID нового юзера
        $rs->Update();
        $this->connection->CommitTrans();
        $data["id"]=(int)$rs->Fields->Item["id"]->Value;
        
        $this->connection->BeginTrans();
        $rs_ext=new RecordSet();
        $rs_ext->Open("select * from users_ext limit 1",$this->connection);
        $rs_ext->AddNew();
        
        //пробежим по расширеной таблице
        foreach ($this->db_field_ext as $field){
            if (in_array($field,$data)){
                $rs_ext->Fields->Item[$field]->Value=$data[$field];
            }
        }
        $rs_ext->Update();
        $this->connection->CommitTrans();
        $rs_ext->Close();
        $rs->Close();
        
        //читаем и заполняем сущность "юзер"
        $this->connection->BeginTrans();
        $rs->$this->connection->Execute("select * from users u,users_ext e where u.id=e.id where u.id=".(int)$data["id"]);
        $this->connection->CommitTrans();
        $user=$rs->FetchEntity(Users::class);
        $rs->Close();
        return $user;
    }
    
    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data) 
    {
        // Do not allow to change user email if another user with such email already exits.
        if($user->getEmail()!=$data['email'] && $this->isUserExists($data['login'])) {
            throw new \Exception("Another user with email address " . $data['email'] . " already exists");
        }
        
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);        
        $user->setStatus($data['status']);        
        
        // Apply changes to database.
        $this->entityManager->flush();

        return true;
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
     * Checks that the given password is correct.
     */
    public function validatePassword($user, $password) 
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();
        
        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     */
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
        $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token;
        
        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";
        
        // Send email to user.
        mail($user->getEmail(), $subject, $body);
    }
    
    /**
     * Checks whether the given password reset token is a valid one.
     */
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
     */
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
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePassword($user, $data)
    {
        $oldPassword = $data['old_password'];
        
        // Check that old password is correct
        if (!$this->validatePassword($user, $oldPassword)) {
            return false;
        }                
        
        $newPassword = $data['new_password'];
        
        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }
        
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        
        // Apply changes
        $this->entityManager->flush();

        return true;
    }
}

