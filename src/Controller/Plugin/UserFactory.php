<?php
/**
* фабрика для плагина контроллеров User, 
* 
*/

namespace Mf\Permissions\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;

use Mf\Permissions\Service\UserManager;

class UserFactory implements FactoryInterface
{
    /**
     * 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $helper = new User();
        if ($container->has(AuthenticationService::class)) {
            $helper->setAuthenticationService($container->get(AuthenticationService::class));
        }
        $helper->setUserManager($container->get(UserManager::class));
        return $helper;

    }
    /**
     * Create and return Acl instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return Acl
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, User::class);
    }

}
