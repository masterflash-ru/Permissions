<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;



/**
фабрика генерации менеджера авторизации
 */
class AuthManagerFactory implements FactoryInterface
{
    /**
     * собственно сам генератор. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $authenticationService = $container->get(AuthenticationService::class);   //сервис авторации
        $sessionManager = $container->get(SessionManager::class);                 //менеджер сессии
        return new $requestedName($authenticationService, $sessionManager);
    }
}
