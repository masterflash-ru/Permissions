<?php
/**
*плагин для контроллеров позволяет делать работать с авторизованным юзером
*/

namespace Mf\Permissions\Controller\Plugin;



use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Exception;

/**
 * 
 */
class User extends AbstractPlugin
{
    /**
    * экземпляр сервиса Mf\Permissions\Service\User
    */
    protected $User;
    
    public function __construct($User)
    {
        $this->User=$User;

    }

    /**
    * просто возвращает экземпляр данного объекта
    * уже программа должна будет обращаться к внутренностям
    */
    public function __invoke()
    {
        return $this;
    }
    
    /*повторяет метод identity плагина zend-mvc-plugin-identity
    * если юзер авторизован, возвращается его ID, если нет то ничего
    */
    public function identity()
    {
        return $this->User->identity();
    }



}