<?php
/**
* фабрика для плагина контроллеров User, 
* 
*/

namespace Mf\Permissions\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
//use Zend\Authentication\AuthenticationService;

use Mf\Permissions\Service\User;

class UserFactory implements FactoryInterface
{
    /**
     * 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        

        return new $requestedName($container->get(User::class));

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
