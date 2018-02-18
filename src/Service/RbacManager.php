<?php
/*
* ПОКА НЕ РАБОТАЕТ!!!!!
*Менеджер контроля доступа, объект который проверяет есть ли доступ данному юзеру к чему-либо.
*главный метод isGranted - который возвращает true/false (да/нет) 

*/

namespace Mf\Permissions\Service;

use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role as RbacRole;

//для заполнения сущностей в стилистике ZF
use Zend\Hydrator\Reflection as ReflectionHydrator;
use ADO\ResultSet\HydratingResultSet;


use Admin\Entity\Users;
use Admin\Entity\Role;


class RbacManager 
{
    private $connection; 

    /**
     * RBAC service.
     * @var Zend\Permissions\Rbac\Rbac
     */
    private $rbac;
    
    /**
     * Auth service.
     * @var Zend\Authentication\AuthenticationService 
     */
    private $authService;
    
    /**
     * Filesystem cache.
     * @var Zend\Cache\Storage\StorageInterface
     */
    private $cache;
    
    /**
     * Assertion managers.
     * @var array
     */
    private $assertionManagers = [];
    

    public function __construct($connection, $authService, $cache, $assertionManagers) 
    {
        $this->connection = $connection;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }
    
    /**
     * инициализация RBAC container.
    * $forceCreate=true - принудительно инициализировать
     */
    public function init($forceCreate = false)
    {
        if ($this->rbac!=null && !$forceCreate) {return;}

        //чистим кеш, если насильно инициализируем
        if ($forceCreate) {
            $this->cache->removeItem('admin_rbac_container');
        }
        
        //пытаемся считать из кеша
        $result = false;
        $this->rbac = $this->cache->getItem('admin_rbac_container', $result);
        if (!$result) {
            //промах кеша, создаем
            $rbac = new Rbac();
            $this->rbac = $rbac;

            $rbac->setCreateMissingRoles(true);

            $rs=$this->connection->Execute("SET SESSION group_concat_max_len = 1000000",$RecordsAffected,adExecuteNoRecords);
            $rs=$this->connection->Execute("select role.*,
              (select group_concat(r.name) from role as r,role_tree as rt where rt.role=role.id and r.id=rt.role ) as parents,
              (select group_concat(p.name) from permission as p,role2permission as r2p where r2p.permission=p.id and r2p.role=role.id ) as permissions 
                  from role");
            $resultSet = new HydratingResultSet(new ReflectionHydrator, new Role);
            $resultSet->initialize($rs);

            //пробежим по всем ролям и загрузим их
            foreach ($resultSet as $role) {
                $roleName = $role->getName();
                $rbac->addRole($roleName, $role->getParentsArray());

                foreach ($role->getPermisionsArray() as $permission) {
                    $rbac->getRole($roleName)->addPermission($permission);
                }
            }

            //сохраним в кеш
            $this->cache->setItem('admin_rbac_container', $rbac);
        }
    }
  
    /**
     * проверка доступа
     *   возвращает true-false разрешено-запрещено
     * @param User|null $user
     * @param string $permission
     * @param array|null $params
     */
    public function isGranted($user, $permission, $params = null)
    {
        //инициализируем если не было инициализации
        if ($this->rbac==null) {
            $this->init();
        }

        if ($user==null) {
            //текущий авторизованный

            $identity = $this->authService->getIdentity(); 
            if ($identity==null) {return false;}

            $identity=(int)$identity;
            $rs=$this->connection->Execute("select r.name from role as r, users2role as u2r where r.id=u2r.role and u2r.users={$identity}");

            if ($rs->EOF) {
                //в сесии есть юзер, но вероятно это подделка
                throw new \Exception('There is no user with such identity');
            }
        } else {
            //указан ID юзера доступ которого нужно проуерить
            $user=(int)$user;
            $rs=$this->connection->Execute("select r.name from role as r, users2role as u2r where r.id=u2r.role and u2r.users={$user}");
          }

        $roles = explode(",",$rs->Fields->Item["name"]->Value);

        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role, $permission)) {
                if ($params==null){return true;}
                foreach ($this->assertionManagers as $assertionManager) {
                      if ($assertionManager->assert($this->rbac, $permission, $params)) {return true;}
                }
                return false;
            }
        }

        return false;
    }
}
