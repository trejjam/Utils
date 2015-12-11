<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 18.6.15
 * Time: 12:17
 */

namespace Trejjam\Utils;


use Nette,
	Trejjam;

interface Exception
{
	const
		UTILS_KEY_NOT_FOUND = 1;
}

class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}

class DomainException extends \DomainException implements Exception
{
}

class LogicException extends \LogicException implements Exception
{
}

class MagicArrayAccessException extends LogicException
{
	protected $usedKeys = [];
	protected $allKeys  = [];
	protected $lastItem = NULL;

	public function setUsedKeys(array $usedKeys)
	{
		$this->usedKeys = $usedKeys;
	}

	public function setAllKeys(array $allKeys)
	{
		$this->allKeys = $allKeys;
	}

	public function setLastItem($lastItem)
	{
		$this->lastItem = $lastItem;
	}

	public function getUsedKeys()
	{
		return $this->usedKeys;
	}

	public function getAllKeys()
	{
		return $this->allKeys;
	}

	public function getLastItem()
	{
		return $this->lastItem;
	}
}
