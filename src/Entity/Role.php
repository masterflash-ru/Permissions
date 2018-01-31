<?php
namespace Mf\Permissions\Entity;


class Role
{
    /**
     * ID роли
     */
    protected $id;

    /** 
     * имя роли 
     */
    protected $name;
    
    /** 
     * описание роли  
     */
    protected $description;

    /** 
     * дата генерации 
     */
    protected $dateCreated;
	protected $parents;
	protected $permissions;

    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

   
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
   
   
   
    public function getParents() 
    {
        return $this->parents;
    }

    public function setParents($parents) 
    {
        $this->parents = $parents;
    }
   
    public function getParentsArray()
    {
		if (empty($this->parents)) return [];
        return explode(",",$this->parents);
    }


    public function getPermisions() 
    {
        return $this->permissions;
    }

    public function setPermisions($permissions) 
    {
        $this->permissions = $permissions;
    }
   
    public function getPermisionsArray()
    {
		if (empty($this->permissions)) return [];
        return explode(",",$this->permissions);
    }
   
}



