<?php
declare(strict_types=1);

namespace Trejjam\Utils\Helpers;

use Trejjam;

/**
 * @method string format(string $format)
 */
class DateTimeModification
{
	/**
	 * @var \DateTimeImmutable|\DateTime
	 */
	private $dateTime;

	public function __construct(\DateTimeInterface $dateTime)
	{
		$this->dateTime = $dateTime;
	}

	public static function modify(\DateTimeInterface $dateTime)
	{
		return new static($dateTime);
	}

	public function getDateTime() : \DateTimeInterface
	{
		return $this->dateTime;
	}

	public function getDateArray() : array
	{
		if ($this->dateTime->getTimestamp() < 0) {
			return [0, 0, 0];
		}

		return explode('-', $this->dateTime->format('Y-m-d'));
	}

	public function getDate() : int
	{
		return intval($this->getDateArray()[2]);
	}

	public function getMonth() : int
	{
		return intval($this->getDateArray()[1]);
	}

	public function getYear() : int
	{
		return intval($this->getDateArray()[0]);
	}

	public function addDay(int $days) : self
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year, $month, $day + $days);

		return $this;
	}

	public function addMonth(int $months, bool $cleanDays = FALSE) : self
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year, $month + $months, $cleanDays ? 1 : $day);

		return $this;
	}

	public function addYear(int $years, bool $cleanMonths = FALSE, bool $cleanDays = FALSE) : self
	{
		list($year, $month, $day) = $this->getDateArray();

		$this->dateTime->setDate($year + $years, $cleanMonths ? 1 : $month, $cleanDays ? 1 : $day);

		return $this;
	}

	public function setDay(int $day) : self
	{
		list($year, $month) = $this->getDateArray();

		$this->dateTime->setDate($year, $month, $day);

		return $this;
	}

	function __call($name, $arguments)
	{
		return call_user_func_array([$this->dateTime, $name], $arguments);
	}
}
