<?php

namespace Isdarka\Cron;

use Isdarka\Utils\DateTime\FixDateTime;
use ArrayIterator;
/**
 *
 * @author isdarka
 *        
 */
class Executable 
{
	/**
	 * 
	 * @var \DateTime
	 */
	private $now;
	
	/**
	 * 
	 * @var \DateTime
	 */
	private $nowPlusFive;
	
	/**
	 * 
	 * @var \ArrayIterator
	 */
	private $dates;
	
	/**
	 * 
	 * @param FixDateTime $now
	 * @param FixDateTime $nowPlusFive
	 */
	public function __construct(FixDateTime $now, FixDateTime $nowPlusFive)
	{
		$this->dates = new ArrayIterator();
		
		$this->now = clone $now;
		$this->nowPlusFive = clone $nowPlusFive;
	}
	
	/**
	 * 
	 * @param string $time
	 * @param string $format
	 */
	public function addTime($time, $format = "H:i:s")
	{
		$dateTime = FixDateTime::fromFormat($format, $time);
		$this->dates->append($dateTime);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isExcutable()
	{
		/* @var $dateTime FixDateTime */
		foreach ($this->dates as $dateTime)
		{
			if($dateTime >= $this->now && $dateTime <= $this->nowPlusFive)
				return true;
		}
		
		return false;
	}
}

?>