<?php

namespace Isdarka\Cron;

use Zend\Db\Adapter\Adapter;
/**
 *
 * @author isdarka
 *        
 */
abstract class AbstractCron implements Cronable 
{
	private $forceExecute = false;
	/**
	 * 
	 * @var Adapter
	 */
	protected $adapter;
	
	/**
	 * 
	 * @var DateTime
	 */
	protected $now;
	
	/**
	 *
	 * @var DateTime
	 */
	protected $nowPlusFive;
	

	/**
	 * 
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\src\Core\Cron\Cronable::run()
	 */
	abstract public function run();
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\src\Core\Cron\Cronable::isExcecutable()
	 */
	abstract public function isExcecutable();
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\src\Core\Cron\Cronable::isActive()
	 */
	abstract public function isActive();
	
	/**
	 * 
	 * @param \DateTime $now
	 */
	public function setNow(\DateTime $now)
	{
		$this->now = $now;
	}
	
	/**
	 * 
	 * @return \DateTime
	 */
	public function getNow()
	{
		return $this->now;
	}
	
	/**
	 * 
	 * @param \DateTime $nowPlusFive
	 */
	public function setNowPlusFive(\DateTime $nowPlusFive)
	{
		$this->nowPlusFive = $nowPlusFive;
	}
	
	/**
	 * 
	 * @return \DateTime
	 */
	public function getNowPlusFive()
	{
		return $this->nowPlusFive;
	}
	
	/**
	 * 
	 * @param Adapter $adapter
	 * @return \Core\Cron\AbstractCron
	 */
	public function setAdapter(Adapter $adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}
	
	/**
	 *
	 * @return Adapter
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	public function getForceExecute() 
	{
		return $this->forceExecute;
	}
	
	/**
	 * 
	 * @param boolean $forceExecute
	 */
	public function setForceExecute($forceExecute)
	{
		$this->forceExecute = $forceExecute;
	}
}