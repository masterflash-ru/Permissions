<?php
/*
Объект который собственно производит авторизацию, 
возвращает экземпляр  Zend\Authentication\Result с результатом авторизации
*/
namespace Mf\Permissions\Service;


use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use Mf\Permissions\Entity\Users;

use ADO\Service\RecordSet;
use ADO\Service\Command;



/**
адаптер аутентификации
 */
class AuthAdapter implements AdapterInterface
{

    private $login;
    private $password;

/**
*соединение с базой
   */
    private $connection;


    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Sets user Login
     */
    public function setLogin($login) 
    {
        $this->login = $login;
    }

    /**
     * Sets password.
     */
    public function setPassword($password) 
    {
        $this->password = (string)$password;
    }

    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
      $c=new Command();
      $c->NamedParameters=true;
      $c->ActiveConnection=$this->connection;
      $p=$c->CreateParameter('login', adChar, adParamInput, 50, $this->login);//генерируем объек параметров
      $c->Parameters->Append($p);//добавим в коллекцию
      $c->CommandText="select * from users where login=:login";

      $rs=new RecordSet();
      $rs->CursorType =adOpenKeyset;
      $rs->Open($c);
        
       $users= $rs->FetchEntity(Users::class);

        // If there is no such user, return 'Identity Not Found' status.
        if ($users == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND, 
                null, 
                ['Пользователь не найден']);
        }

        $bcrypt = new Bcrypt();
        $passwordHash = $users->getPassword();//хеш пароля из базы

        if ($bcrypt->verify($this->password, $passwordHash)) {
            //успешная авторизация, возвращаем успех и ID записи из таблицы админов
            return new Result(
                    Result::SUCCESS, 
                    $users->getId(), 
                    ['Авторизация успешна']);
        }

        return new Result(
                Result::FAILURE_CREDENTIAL_INVALID, 
                null, 
                ['Неверный пароль или иная ошибка']);
    }

}
