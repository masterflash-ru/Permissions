<?php
/*
*менеджер авторизации, объект который сохраняет результат успешной авторизации в хранилище, сессии
данный объект вызывает Mf\Permissions\Service\AuthAdapter, передает туда логин/пароль и получает ответ, этот ответ возвращается в программу которая
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
    const ACCESS_GRANTED = 1; //доступн разрешен
    const AUTH_REQUIRED  = 2; //перейти на страницу авторизации
    const ACCESS_DENIED  = 3; //доступ запрещен

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
    public function __construct($authService, $sessionManager, $config,$rbacManager) 
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
        $this->rbacManager = $rbacManager;
    }
    
    /**
     * авторизация и сохранение в сессии
     * 
     */
    public function login($login, $password, $rememberMe=false)
    {   

        // авторизация login/password через адаптер
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setLogin($login);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        // If user wants to "remember him", we will make session to expire in 
        // one month. By default session expires in 1 hour (as specified in our 
        // config/global.php file).
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
        // Allow to log out only when user is logged in.
        if ($this->authService->getIdentity()==null) {
            throw new Exception('The user is not logged in');
        }

        // Remove identity from session.
      $this->authService->clearIdentity();
    }



    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     * 
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     */
public function filterAccess($controllerName, $actionName)
    {
        /* Determine mode - 'restrictive' (default) or 'permissive'.
        * всем, если мы поставим звездочку (*);
        * любому аутентифицированному пользователю, если мы поставим коммерческое at (@);
        * конкретному аутентифицированному пользователю с заданным адресом эл. почты личности, если мы поставим (@identity)
        * любому аутентифицированному пользователю с заданной привилегией, если мы поставим знак плюса и имя привилегии (+permission).
*/


      $mode = isset($this->config['options']['mode'])?$this->config['options']['mode']:'restrictive';

      if ($mode!='restrictive' && $mode!='permissive') {throw new Exception('Invalid access filter mode (expected either restrictive or permissive mode');}

      if (isset($this->config['controllers'][$controllerName])) {
            $items = $this->config['controllers'][$controllerName];
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                if (is_array($actionList) && in_array($actionName, $actionList) || $actionList=='*') {
                    if ($allow=='*'){
                          //разрешено все
                          return self::ACCESS_GRANTED; 
                    }
                      else if (!$this->authService->hasIdentity()) {
                        //юзер не авторизован вообще, предложить авторизоваться
                        return self::AUTH_REQUIRED;
                      }

                    if ($allow=='@') {
                        // разрешено любому авторизованном
                        return self::ACCESS_GRANTED;
                    } else if (substr($allow, 0, 1)=='@') {
                        // разрешено только юзеру с указанным ID
                        $identity = substr($allow, 1);
                        if ($this->authService->getIdentity()==$identity){return self::ACCESS_GRANTED; }
                          else { return self::ACCESS_DENIED; }
                    } else if (substr($allow, 0, 1)=='+') {
                          //любой авторизованный юзер у которого есть эта привелегия
                          $permission = substr($allow, 1);
                          if ($this->rbacManager->isGranted(null, $permission)) {return self::ACCESS_GRANTED; }
                            else  {return self::ACCESS_DENIED;}
                    } else { throw new \Exception('Unexpected value for "allow" - expected either "?", "@", "@identity" or "+permission"'); }
                }
            }
        }

        // In restrictive mode, we require authentication for any action not 
        // listed under 'access_filter' key and deny access to authorized users 
        // (for security reasons).
        if ($mode=='restrictive') {
            if(!$this->authService->hasIdentity()) { return self::AUTH_REQUIRED;}
                else { return self::ACCESS_DENIED;}
        }

        // Permit access to this page.
        return self::ACCESS_GRANTED;
    }

}