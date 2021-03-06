<?php
/**
 *
 * @author isdarka
 * @created Dec 9, 2013 6:26:10 PM
 */

namespace Isdarka\Security;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Core\Query\RoleQuery;
use Core\Query\ControllerQuery;
use Core\Query\ActionQuery;
use Core\Model\Bean\User;
use Core\Model\Bean\Controller;

class Acl extends ZendAcl
{
	private $adapter;
	private $role;
	private $user;
	public function __construct($adapter,User $user)
	{
		$this->adapter = $adapter;
		$this->user = $user;
	}
	
	private function setRoles()
	{
		$roleQuery = new RoleQuery($this->adapter);
		$role = $roleQuery->findByPk($this->user->getIdRole());
		$this->role = $role;
		$this->addRole(new Role($role->getIdRole()));
	}
	
	private function setResources()
	{
		
		$controllerQuery = new ControllerQuery($this->adapter);
		$actionQuery = new ActionQuery($this->adapter);
		$actionQueryAllowed = new ActionQuery($this->adapter);
		$actionsAllowed = $actionQueryAllowed->innerJoinRole()
		->whereAdd(\Core\Model\Bean\Role::ID_ROLE, $this->role->getIdRole())->find();
		
		$actions = $actionQuery->find();
		$controllers = $controllerQuery
		->whereAdd(Controller::ID_CONTROLLER, $actions->getControllerIds(), ControllerQuery::IN)
		->find();
		
		
		foreach ($controllers as $controller)
		{
			$controllerActions = $actions->getByIdController($controller->getIdController());
			$controllerName = substr($controller->getName(), 0, -11);
			foreach ($controllerActions as $action)
			{
				$actionName = substr($action->getName(), 0, -7);
				$resource = strtolower($controllerName) . "::" . strtolower($actionName);
				if(!$this->hasResource($resource))
					$this->addResource(new Resource($resource));
				
				if(in_array($action->getIdAction(), $actionsAllowed->getPrimaryKeys()))
					$this->allow($this->role->getIdRole(), $resource);
				else
					$this->deny($this->role->getIdRole(), $resource);
			}
			
		}
	}
	
	public function removeAll()
	{
		$this->removeResourceAll();
		$this->removeRoleAll();
	}
	
	public function flushPrivileges()
	{
		$this->setRoles();
		$this->setResources();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getAcl()
	{
		$this->adapter = NULL;
		return $this;
	}
	
}