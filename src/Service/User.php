<?php
namespace Mf\Permissions\Service;

use Mf\Permissions\Entity\Users;
use Zend\Authentication\AuthenticationServiceInterface;
use Exception;


/**
 * сервис для управления авторизованным юзером
 */
class User
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

