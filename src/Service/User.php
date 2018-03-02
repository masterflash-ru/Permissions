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
    * хранит объект системного кеша
    */
    //protected $Cache;

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

    /*установить Cache* /
    public function setCache($Cache)
    {
        $this->Cache=$Cache;
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

        return (int)$this->authenticationService->getIdentity();

    }
    /**
    * получить ID авторизованного юзера, эквивалент identity()
    * возвращает int число>0
    */
    public function getUserId()
    {
        return $this->identity();
    }
    
    /**
    * получить ID группы авторизованного юзера
    * возвращает массив ID групп которым принадлежит юзер
    */
    public function getGroupIds()
    {
        return $this->UserManager->getGroupIds($this->identity());
    }


}

