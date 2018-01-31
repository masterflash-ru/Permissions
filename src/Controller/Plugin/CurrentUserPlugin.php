<?php
namespace Mf\Permissions\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Mf\Permissions\Entity\Users;

/**
 * This controller plugin is designed to let you get the currently logged in User entity
 * inside your controller.
 */
class CurrentUserPlugin extends AbstractPlugin
{
    private $connection;
    
    /**
     * Authentication service.
     * @var Zend\Authentication\AuthenticationService 
     */
    private $authService;
    
    /**
     * Logged in user.
     * @var Admin\Entity\User
     */
    private $user = null;
    
    /**
     * Constructor. 
     */
    public function __construct($connection, $authService) 
    {
        $this->connection = $connection;
        $this->authService = $authService;
    }

    /**
     * This method is called when you invoke this plugin in your controller: $user = $this->currentUser();
     * @param bool $useCachedUser If true, the User entity is fetched only on the first call (and cached on subsequent calls).
     * @return User|null
     */
    public function __invoke($useCachedUser = true)
    {        
        // If current user is already fetched, return it.
        if ($useCachedUser && $this->user!==null)
            return $this->user;
        
        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {
            
            // Fetch User entity from database.
            $this->user = $this->entityManager->getRepository(User::class)
                    ->findOneByEmail($this->authService->getIdentity());
            if ($this->user==null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem. 
                throw new \Exception('Not found user with such email');
            }
            
            // Return found User.
            return $this->user;
        }
        
        return null;
    }
}



