<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;
use Mf\Permissions\Service\AuthAdapter;


/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
    *
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Simba_Auth', 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        return new $requestedName($authStorage, $authAdapter);
    }
}

