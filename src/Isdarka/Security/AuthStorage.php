<?php
/**
 *
 * @author isdarka
 * @created Dec 8, 2013 8:52:06 PM
 */

namespace Isdarka\Security;

use Zend\Authentication\Storage\Session;
class AuthStorage extends Session
{
	public function setRememberMe($rememberMe = false, $time = 1209600)
	{
		if ($rememberMe) {
			$this->getSessionManager()->rememberMe($time);
		}
	}
	 
	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
	
	/**
	 * 
	 * @return \Zend\Session\SessionManager
	 */
	public function getSessionManager()
	{
		return $this->session->getManager();
	}
}