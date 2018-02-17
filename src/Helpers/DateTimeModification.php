<?php

namespace Trejjam\Utils\Helpers;

use Trejjam;

/**
 * @method string format(string $format)
 */
class DateTimeModification
{
	/**
	 * @var \DateTime
	 */
	private $dateTime;

	public function __construct(\DateTime $dateTime)
	{
		$this->dateTime = $dateTime;
	}

	public static function modify(\DateTime $dateTime)
	{
		return new static($dateTime);
	}

	public function getDateTime()
	{
		return $this->dateTime;
	}

	public function getDateArray()
	{
		if ($this->dateTime->getTimestamp() < 0) {
			return [0, 0, 0];
		}

		return explode('-', $this->dateTime->format('Y-m-d'));
	}

	public function getDate()
	{
		return $this->getDateArray()[2];
	}

	public function getMonth()
	{
		return $this->getDateArray()[1];
	}

	public function getYear()
	{
		return $this->getDateArray()[0];
	}

	public function addDay($days)
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year, $month, $day + $days);

		return $this;
	}

	public function addMonth($months, $cleanDays = FALSE)
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year, $month + $months, $cleanDays ? 1 : $day);

		return $this;
	}

	public function addYear($years, $cleanMonths = FALSE, $cleanDays = FALSE)
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year + $years, $cleanMonths ? 1 : $month, $cleanDays ? 1 : $day);

		return $this;
	}

	public function setDay($day){
		list($year, $month) = $this->getDateArray();

		$this->dateTime->setDate($year, $month, $day);

		return $this;
	}

	function __call($name, $arguments)
	{
		return call_user_func_array([$this->dateTime, $name], $arguments);
	}
}
