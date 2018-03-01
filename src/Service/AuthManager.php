<?php
/*
*менеджер авторизации, объект который обращается в адаптер авторизации и сохраняет результат успешной авторизации в хранилище, сессии
*данный объект вызывает Mf\Permissions\Service\AuthAdapter, передает туда логин/пароль и получает ответ, 
*этот ответ возвращается в программу которая
*вызвала, возвращается Zend\Authentication\Result
*/

namespace Mf\Permissions\Service;

use Zend\Authentication\Result;
use Exception;




/**
менеджер аутентификации, он вызывает адаптер
 */
class AuthManager
{
    //const ACCESS_GRANTED = 1; //доступн разрешен
    //const AUTH_REQUIRED  = 2; //перейти на страницу авторизации
    //const ACCESS_DENIED  = 3; //доступ запрещен

    /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * Session manager.
     * @var Zend\Session\SessionManager
     */
    private $sessionManager;
    
    /**
     * 
     * @var array 
     */
    private $config;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $sessionManager) 
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }
    
    /**
     * авторизация и сохранение в сессии
     * по умолчанию сохраняет сессию на 30 дней, если выбрано "запомнить меня"
     */
    public function login($login, $password, $rememberMe=false)
    {   

        // авторизация login/password через адаптер
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setLogin($login);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        if ($result->getCode()==Result::SUCCESS && $rememberMe) {
            $this->sessionManager->rememberMe(60*60*24*30);
        }

        return $result;
    }

    /**
     * выход
     */
    public function logout()
    {
        if ($this->authService->getIdentity()==null) {
            throw new Exception('Пользователь не авторизован');
        }

      $this->authService->clearIdentity();
    }



}