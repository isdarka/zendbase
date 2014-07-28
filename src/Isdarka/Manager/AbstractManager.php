<?php

namespace Isdarka\Manager;

use Zend\Db\Adapter\Adapter;
/**
 *
 * @author isdarka
 *        
 */
abstract class AbstractManager  
{
	private $adapter;
	
	/**
	 * 
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;	
	} 
	
	/**
	 * 
	 * @return Adapter
	 */
	protected function getAdapter() 
	{
		return $this->adapter;
	}
}