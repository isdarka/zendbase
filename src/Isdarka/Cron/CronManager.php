<?php

namespace Isdarka\Cron;

use Zend\Db\Adapter\Adapter;
use ArrayIterator;
use Isdarka\Utils\DateTime\FixDateTime;
use DateInterval;
/**
 *
 * @author isdarka
 *        
 */
final class CronManager
{
	private $forceExecute = false;
	protected $cronables;
	
	public function __construct(Adapter $adapter) {
		$this->cronables = new ArrayIterator();
	}
	
	/**
	 *
	 * @param Cronable $cronable
	 */
	public function addCronable(Cronable $cronable)
	{
		$this->cronables->append($cronable);
	}
	
	/**
	 * 
	 * @return \ArrayIterator
	 */
	public function getCronables()
	{
		return $this->cronables;
	}
	
	public function run()
	{
		$now = new FixDateTime("now");
		$nowPlusFive = clone $now;
		$nowPlusFive->add(new DateInterval("PT5M"));
		
		/* @var $cronable AbstractCron */
		foreach ($this->getCronables() as $cronable)
		{
			$cronable->setForceExecute($this->getForceExecute());
			$cronable->setNow($now);
			$cronable->setNowPlusFive($nowPlusFive);
			
			if($cronable->getForceExecute())
				$cronable->run();
			else{
				if($cronable->isActive() && $cronable->isExcecutable()){
					$cronable->run();
				}	
			}
			
		}
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

?>