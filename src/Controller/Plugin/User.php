<?php
/**
*плагин для контроллеров позволяет делать работать с авторизованным юзером
*/

namespace Mf\Permissions\Controller\Plugin;


use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Exception;

/**
 * 
 */
class User extends AbstractPlugin
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authenticationService;
    
    /**
    * экземпляр UserManager - он работает со всеми юзеарми
    * в него передается отсюда все для работы с авторизованным
    */
    protected $UserManager;

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function setAuthenticationService(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /*установить UserManager*/
    public function setUserManager($UserManager)
    {
        $this->UserManager=$UserManager;
    }
    
    /*получить UserManager*/
    public function getUserManager()
    {
        return $this->UserManager;
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
        if (! $this->authenticationService instanceof AuthenticationServiceInterface) {
            throw new Exception(
                'No AuthenticationServiceInterface instance provided; cannot lookup identity'
            );
        }

        if (! $this->authenticationService->hasIdentity()) {
            return;
        }

        return $this->authenticationService->getIdentity();

    }



}