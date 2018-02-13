<?php
namespace Mf\Permissions\Entity;


class Users
{

/*
Карта имен полей таблицы и имен местных переменных
по формату:
имя в таблице => имя в этом объекте
* /

private static $__map__=[
	"fields"=>[
			/*Имя поля таблицы => имя в этой сущности + параметры* /
			"id"=>["name"=>"id","type"=>"int","length"=>11],
			"email"=>["name"=>"email"],
			"name"=>["name"=>"name"],
			"pass"=>["name"=>"pass"],
			"fullname"=>["name"=>"fullname","type"=>"string","length"=>100],
			"email"=>["name"=>"email"],
			"tel_mobil"=>["name"=>"tel_mobil"]
			],
	"table"=>"Admins"
	];
*/
	const STATUS_ACTIVE       = 1; //нормальное состояние
    const STATUS_NONACTIVE    = 0; //не активный.
	
	
protected $id = null;

    protected $login = null;

    protected $status = null;

    protected $phone = null;

    protected $password = null;

    protected $name = null;

    protected $full_name = null;

    protected $temp_password = null;

    protected $temp_date = null;

    protected $confirm_hash = null;

    protected $date_registration = null;

    protected $date_last_login = null;

    public function setId($id)
    {
        $this->id=$id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLogin($login)
    {
        $this->login=$login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setStatus($status)
    {
        $this->status=$status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setPhone($phone)
    {
        $this->phone=$phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPassword($password)
    {
        $this->password=$password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setName($name)
    {
        $this->name=$name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setFull_name($full_name)
    {
        $this->full_name=$full_name;
    }

    public function getFull_name()
    {
        return $this->full_name;
    }

    public function setTemp_password($temp_password)
    {
        $this->temp_password=$temp_password;
    }

    public function getTemp_password()
    {
        return $this->temp_password;
    }

    public function setTemp_date($temp_date)
    {
        $this->temp_date=$temp_date;
    }

    public function getTemp_date()
    {
        return $this->temp_date;
    }

    public function setConfirm_hash($confirm_hash)
    {
        $this->confirm_hash=$confirm_hash;
    }

    public function getConfirm_hash()
    {
        return $this->confirm_hash;
    }

    public function setDate_registration($date_registration)
    {
        $this->date_registration=$date_registration;
    }

    public function getDate_registration()
    {
        return $this->date_registration;
    }

    public function setDate_last_login($date_last_login)
    {
        $this->date_last_login=$date_last_login;
    }

    public function getDate_last_login()
    {
        return $this->date_last_login;
    }

}
