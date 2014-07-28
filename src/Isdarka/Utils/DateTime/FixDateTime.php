<?php

namespace Isdarka\Utils\DateTime;

use DateTime;
use DateTimeZone;
/**
 *
 * @author isdarka
 *        
 */
class FixDateTime extends DateTime 
{
	const DATE = 'd-m-Y';
	const DATETIME = 'd-m-Y H:i:s';
	
	const MYSQL_DATE = 'Y-m-d';
	const MYSQL_DATETIME = 'Y-m-d H:i:s';
	
	/**
	 * @return string
	 */
	public function getDate()
	{
		return $this->format(self::DATE);
	}
	
	/**
	 * @return string
	 */
	public function getDateTime()
	{
		return $this->format(self::DATETIME);
	}
	
	/**
	 * @return string
	 */
	public function getMysqlDate()
	{
		return $this->format(self::MYSQL_DATE);
	}
	
	/**
	 * @return string
	 */
	public function getMysqlDateTime()
	{
		return $this->format(self::MYSQL_DATETIME);
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->format(self::MYSQL_DATETIME);
	}
	
	/**
	 * 
	 * @param array $array
	 * @param array $dates
	 * @param string $formatIn
	 * @param string $formatOut
	 * @return array
	 */
	public static function fixDates(array $array, array $dates, $formatIn = self::DATE, $formatOut = self::MYSQL_DATE)
	{
		foreach ($array as $key => $value)
			if(in_array($key, $dates))
			{
				/* @var $date DateTime */
				$date = DateTime::createFromFormat($formatIn, $value);
				$array[$key] = $date->format($formatOut);
			}
		return $array;
	}
	
	/**
	 * 
	 * @param string $date
	 * @param string $formatIn
	 * @param string $formatOut
	 * @return string
	 */
	public static function fixDate($date, $formatIn = self::DATE, $formatOut = self::MYSQL_DATE)
	{
		if(empty($date))
			return $date;
		/* @var $date DateTime */
		$date = DateTime::createFromFormat($formatIn, $date);
		return $date->format($formatOut);
	}
	
	public static $moths = array(
			self::JANUARY => "Enero",
			self::FEBRUARY => "Febrero",
			self::MARCH => "Marzo",
			self::APRIL => "Abril",
			self::MAY => "Mayo",
			self::JUNE => "Junio",
			self::JULY => "Julio",
			self::AUGUST => "Agosto",
			self::SEPTEMBER => "Septiembre",
			self::OCTOBER => "Octubre",
			self::NOVEMBER => "Noviembre",
			self::DECEMBER => "Diciembre",
		
	);
	
	const JANUARY = 1;
	const FEBRUARY = 2;
	const MARCH = 3;
	const APRIL = 4;
	const MAY = 5;
	const JUNE = 6;
	const JULY = 7;
	const AUGUST = 8;
	const SEPTEMBER = 9;
	const OCTOBER = 10;
	const NOVEMBER = 11;
	const DECEMBER = 12;
	
	
	public function getLastTwelveMonths()
	{
		$currentMonth = $this->format("n");
		$months = array();
		$j = 0;
		for ($i = $currentMonth; $i >= 1; $i--)
		{
			$months[$j] = self::$moths[$i];
			$j++;
		}
			
		
		for ($i = self::DECEMBER; $i > $currentMonth; $i--)
		{
			$months[] = self::$moths[$i];
			$j++;
		}
			
		return $months;
	}
	
	/**
	 * 
	 * @param string $format
	 * @param string $time
	 * @param string $object
	 * @return FixDateTime
	 */
	public static function fromFormat($format, $time, $object = null) {
	
		if($object instanceof DateTimeZone)
			$dateTime = parent::createFromFormat($format, $time, $object);
		else
			$dateTime = parent::createFromFormat($format, $time);
		
		/* @var $dateTime DateTime */
		$static = new static();
		$static->setDate($dateTime->format("Y"), $dateTime->format("m"), $dateTime->format("d"));
		$static->setTime($dateTime->format("H"), $dateTime->format("i"), $dateTime->format("s"));
		return $static;
	}
}

?>