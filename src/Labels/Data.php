<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 18.6.15
 * Time: 14:30
 */

namespace Trejjam\Utils\Labels;


use Nette,
	Trejjam;

class Data
{
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var Labels
	 */
	protected $labels;

	public function __construct($name, Labels $labels)
	{
		$this->name = $name;
		$this->labels = $labels;
	}

	public function __toString()
	{
		return $this->labels->getData($this->name);
	}
	public function &__get($name)
	{
		$labelData = $this->labels->getData($name, $this->name);

		return $labelData;
	}
	public function __set($name, $data)
	{
		$this->labels->setData($name, $data, $this->name);

		return $data;
	}
}
