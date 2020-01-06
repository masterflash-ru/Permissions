<?php
/**
* фабрика для плагина контроллеров ACL, передает экземпляр Mf\Permissions\Service\Acl в конструктор плагина
* через сервис Mf\Permissions\Service\Acl производится взаимодействие
*/

namespace Mf\Permissions\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

use Mf\Permissions\Service\Acl;


class AclFactory implements FactoryInterface
{
    /**
     * 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $acl=$container->get(Acl::class);
        return new $requestedName($acl);
    }
    /**
     * Create and return Acl instance
     *
     * For use with Laminas-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return Acl
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Acl::class);
    }

}
