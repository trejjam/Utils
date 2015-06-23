<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 22.6.15
 * Time: 16:17
 */

namespace Trejjam\Utils\Contents\Items;


abstract class SubType
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @internal
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Enable usage in items
	 * @param Base $base
	 * @return bool
	 */
	public abstract function applyOn(Base $base);

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public abstract function sanitize($data);

	/**
	 * @param $rawData
	 * @param $data
	 * @return NULL|FALSE NULL - without change result, FALSE - force NULL result
	 */
	public function removedContent($rawData, $data)
	{
		return NULL;
	}
}
