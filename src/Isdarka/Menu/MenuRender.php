<?php
/**
 *
 * @author isdarka
 * @created Dec 9, 2013 12:18:22 PM
 */

namespace Isdarka\Menu;

use Core\Query\MenuItemQuery;
use Core\Model\Bean\MenuItem;
use Core\Model\Collection\MenuItemCollection;
use Core\Model\Bean\User;
use Core\Query\ControllerQuery;
use Core\Query\ActionQuery;
use Core\Model\Bean\Role;
use Core\Query\RoleQuery;
use Core\Model\Collection\ActionCollection;
use Core\Model\Bean\Action;
use Core\Model\Bean\Controller;
use Core\Query\FileQuery;
use Core\Model\Bean\File;
use Core\Helper\File\Upload;
use Zend\Mvc\I18n\Translator;
use Core\Model\Collection\ControllerCollection;
class MenuRender
{
	private $adapter;
	private $parents;
	private $baseUrl;
	private $menuItems;
	private $user;
	private $role;
	private $availableActions;
	private $i18n;
	
	//////
	private $menuItemCollection;
	private $controllerCollection;
	private $actionCollection;
	/**
	 * 
	 * @return Translator
	 */
	public function getI18n() {
		return $this->i18n;
	}

	/**
	 * 
	 * @param Translator $i18n
	 */
	public function setI18n(Translator $i18n) {
		$this->i18n = $i18n;
	}

	public function __construct($adapter, $baseUrl, User $user)
	{
		$this->adapter = $adapter;
		$this->baseUrl = $baseUrl;
		$this->user = $user;
	}
	
	/**
	 * 
	 * @return Role
	 */
	public function getRole()
	{
		if($this->role instanceof Role == false)
		{
			$roleQuery = new RoleQuery($this->adapter);
			$this->role = $roleQuery->findByPk($this->user->getIdRole());
		}	
		return $this->role;
	}
	
	
	public function getCamelCase($string)
	{
		return lcfirst(join("", array_map("ucwords", explode("_", $string))));
	}
	
	public function getUnderscore($string)
	{
		return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '-$1', $string));
	}
	
// 	/**
// 	 * @return MenuItemCollection
// 	 */
// 	private function getParents()
// 	{
// 		if($this->parents instanceof MenuItemCollection)
// 			return $this->parents;
		
// 		$menuItemQuery = new MenuItemQuery($this->adapter);
// 		$menuItemQuery->whereAdd(MenuItem::ID_PARENT, null, MenuItemQuery::IS_NULL);
// 		$menuItemQuery->whereAdd(MenuItem::STATUS, MenuItem::ENABLE);
// 		$menuItemQuery->whereAdd(MenuItem::ID_MENU_ITEM, 9);
// 		$menuItemQuery->addAscendingOrderBy(MenuItem::ORDER);
// 		$menuItems = $menuItemQuery->find();

// 		$this->parents = $menuItems;
// 		return $this->parents;
// 	}
	
// 	/**
// 	 * @return ActionCollection
// 	 */
// 	private function getAvailableActions()
// 	{
// 		if($this->availableActions instanceof ActionCollection == false)
// 		{
// 			$actionQuery = new ActionQuery($this->adapter);
// 			$actionQuery->innerJoinRole();
// 			$actionQuery->whereAdd(Role::ID_ROLE, $this->getRole()->getIdRole());
// 			$this->availableActions = $actionQuery->find();
// 		}
// 		return $this->availableActions;
// 	}
	
// 	private function getMenuItems()
// 	{
// 		$menuItemQuery = new MenuItemQuery($this->adapter);
// 		$menuItemQuery->whereAdd(MenuItem::STATUS, MenuItem::ENABLE);
// 		$menuItemQuery->addAscendingOrderBy(MenuItem::ORDER);
// 		$menuItems = $menuItemQuery->find();
// 		$this->menuItems = $menuItems;
// 	}
	
	/**
	 * 
	 * @return string
	 */
	private function getHtml()
	{
		$html = '';
		$menuItemsParents = $this->getMenuItemCollection()->getByIdParent(null);
		/* @var $menuItem MenuItem */
		foreach ($menuItemsParents as $menuItem)
		{
			$html .= '<li class="dropdown">';
			if($this->menuItemHasChilds($menuItem))
			{
				$subMenus = $this->getSubMenus($menuItem);
				if($subMenus)
				{
					$a = '<a class="dropdown-toggle" data-toggle="dropdown" href="' . $this->getPathByMenuItem($menuItem) . '">' . $menuItem->getName();
					$html .= $a . '<b class="caret"></b></a>';
					$html .= '<ul class="dropdown-menu">';
					$html .= $this->getSubMenus($menuItem);
					$html .= '</ul>';
				}
				
			}else{
				$a = '<a href="' . $this->getPathByMenuItem($menuItem) . '">' . $menuItem->getName();
				$html .= $a . '</a>';
			}
			$html .= '</li>';
		}
		
		return $html;
	}
	
	
	/**
	 * @author isdarka
	 * Recursive function for sub sub .... menus
	 * @param MenuItem $menuItem
	 * @param string $html
	 * @return string
	 */
	private function getSubMenus(MenuItem $menuItem, $html = NULL)
	{
		$menuItemCollection = $this->getMenuItemCollection()->getByIdParent($menuItem->getIdMenuItem());
		foreach ($menuItemCollection as $menuItem)
		{
			if(!is_null($this->getPathByMenuItem($menuItem)))
			{
					
				if($this->menuItemHasChilds($menuItem))
				{
					$subMenus = $this->getSubMenus($menuItem);
					if($subMenus)
					{
						$a = '<a class="dropdown-toggle" data-toggle="dropdown" href="' . $this->getPathByMenuItem($menuItem) . '">' . $menuItem->getName() . '</a>';
						$html .= '<li class="dropdown-submenu">';
						$html .= $a;
						$html .= '<ul class="dropdown-menu">';
						$html .= $this->getSubMenus($menuItem);
						$html .= '</ul>';
						$html .= '</li>';
					}
					
				}else{
					$a = '<a href="' . $this->getPathByMenuItem($menuItem) . '">' . $menuItem->getName() . '</a>';
					$html .= '<li>';
					$html .= $a;
					$html .= '</li>';
				}
				
			}
		}
		return $html;
	}
	
	public function render()
	{
		$html = '';
		$html .= '<div class="collapse navbar-collapse">';
		$html .= '<ul class="nav navbar-nav">';
		$html .= $this->getHtml();
		$html .= '</ul>';
		$html .= $this->getUserMenu();
		$html .= '</div>';
		
		return $html;
	}
	
	private function getUserMenu()
	{
		
		$fileQuery = new FileQuery($this->adapter);
		if($this->user->getIdFile())
			$file = $fileQuery->findByPK($this->user->getIdFile());
		else
		{
			$file = new File();
			$file->setName('no_image.png');
		}
			
		$file->setPath(Upload::$publicDestinations[Upload::AVATAR]);
		
		$html = '';
		$html .= '<ul class="nav navbar-nav navbar-right">';
		
// 		$html .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" id="comments" href="#"><i class="fa fa-comments-o fa-lg"></i> <span class="badge">3</span></a>';
// 		$html .= '<ul class="dropdown-menu comments" style="width: 200px;">';
// 		$html .= '<li style="height: 50px;"></li>';
// 		$html .= '<li role="presentation" class="divider"></li>';
// 		$html .= '<li>';
// 		$html .= '<input type="text" class="form-control input-sm" id="inputComment" name="inputComment" value="" >';
// 		$html .= '</li>';
// 		$html .= '</ul>';
// 		$html .= '</li>';
		
		
		$html .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" id="notification" href="#"><i class="fa fa-exclamation-triangle fa-lg"></i> <span class="badge"></span> <b class="caret"></b></a>';
		$html .= '<ul class="dropdown-menu msnNotifications" >';
		$html .= '<li role="presentation" class="dropdown-header"><a>You have 0 new notifications</a></li>';
		$html .= '<li role="presentation" class="divider"></li>';
// 		$html .= '<li>
// 				<div class="row">
// 					<div class="col-sm-4 text-center">
// 						<img  src="'. $this->baseUrl. '/' . $file->getPath() .'/' . $file->getName() . '" width="50">
// 					</div>	
// 					<div class="col-sm-8">
// 						asdasd asdas asd asdassasdasd asd
// 					</div>
// 				</div>
// 				</li>';
		$html .= '</ul>';
		$html .= '</li>';
		$html .= '<li><a class="dropdown-toggle" data-toggle="dropdown" id="newMessage" href="#"><i class="fa fa-envelope-o fa-lg"></i></a></li>';
		
		$html .= '<li class="dropdown"><a href="#" class="dropdown-toggle gear" data-toggle="dropdown" >' . $this->user->getFullName(). '  <i class="fa fa-cog fa-lg"></i> <b class="caret"></b></a>';
		$html .= '<ul class="dropdown-menu">';
		$html .= '<li class="text-center"><img  src="'. $this->baseUrl. '/' . $file->getPath() .'/' . $file->getName() . '" width="50"></li>';
// 		$html .= '<li role="presentation" class="dropdown-header">You have 9 new notifications</li>';
		$html .= '<li role="presentation" class="divider"></li>';
		$html .= '<li><a href="'. $this->baseUrl. '/core/user/profile"><i class="fa fa-user"></i> ' . $this->getI18n()->translate('Profile') . '</a></li>';
		$html .= '<li><a href="'. $this->baseUrl. '/core/message/my-messages"><i class="fa fa-envelope-o"></i> ' . $this->getI18n()->translate('Messages') . '</a></li>';
		$html .= '<li><a href="'. $this->baseUrl. '/core/user/change-password"><i class="fa fa-key"></i> ' . $this->getI18n()->translate('Change Password') . '</a></li>';
		$html .= '<li role="presentation" class="divider"></li>';
		$html .= '<li><a href="'. $this->baseUrl. '/core/auth/login"><i class="fa fa-power-off"></i> ' . $this->getI18n()->translate('LogOut') . '</a></li>';
		$html .= '</ul>';
		$html .= '</li>';

		
		
		
		
// 		$html .= '<li class="dropdown">';
// 			$html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $this->user->getFullName(). ' <b class="caret"></b></a>';
// 			$html .= '<ul class="dropdown-menu">';
// 				$html .= '<li><a href="'. $this->baseUrl. '/core/auth/login">LogOut</a></li>';
// 				$html .= '<li><a href="'. $this->baseUrl. '/core/user/change-password">Change Password</a></li>';
				
// 			$html .= '</ul>';
// 		$html .= '</li>';
		$html .= '</ul>';
		
		return $html;
	}
	
	
	
	// New Version
	/**
	 * 
	 * @return MenuItemCollection
	 */
	private function getMenuItemCollection()
	{
		if($this->menuItemCollection instanceof MenuItemCollection)
			return $this->menuItemCollection;
		
		$menuItemQuery = new MenuItemQuery($this->adapter);
		$menuItemQuery->addAscendingOrderBy(MenuItem::ORDER);
		$menuItemQuery->whereAdd(MenuItem::STATUS, MenuItem::ENABLE);
		$this->menuItemCollection = $menuItemQuery->find();
		
		return $this->menuItemCollection;
	}
	
	/**
	 * 
	 * @return ControllerCollection
	 */
	private function getControllerCollection()
	{
		if($this->controllerCollection instanceof ControllerCollection)
			return $this->controllerCollection;
		
		$controllerQuery = new ControllerQuery($this->adapter);
		$this->controllerCollection = $controllerQuery->find();
		
		return $this->controllerCollection;
	}
	
	/**
	 * 
	 * @return ActionCollection
	 */
	private function getActionCollection()
	{
		if($this->actionCollection instanceof ActionCollection)
			return $this->actionCollection;
		
		$actionQuery = new ActionQuery($this->adapter);
		$actionQuery->innerJoinRole();
		$actionQuery->whereAdd(Role::ID_ROLE, $this->getRole()->getIdRole());
		$this->actionCollection = $actionQuery->find();
		
		return $this->actionCollection;
	}
	
	private function getPathByMenuItem(MenuItem $menuItem)
	{
		if($menuItem->getIdAction())
		{
			/* @var $action Action */
			$action = $this->getActionCollection()->getByPK($menuItem->getIdAction());
			if($action instanceof Action)
			{
				$controller = $this->getControllerCollection()->getByPK($action->getIdController());
			
				$path = str_replace("controller", "", $controller->getName()) . "/" . str_replace("-action", "", $action->getName());
				$path = str_replace("\\", "/", $path);
				$path = str_replace("//", "/", $path);
				$path = str_replace("-/", "/", $path);
				$path = $this->getUnderscore($path);
				
				return $this->baseUrl . '/' . $path;
			}else 
				return null;
		}else 
			return "#";	
	}
	
	private function menuItemHasChilds(MenuItem $menuItem)
	{
		$menuItemCollection = $this->getMenuItemCollection()->getByIdParent($menuItem->getIdMenuItem());
		return !$menuItemCollection->isEmpty();
	}
}