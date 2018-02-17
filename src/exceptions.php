<?php
declare(strict_types=1);

namespace Trejjam\Utils;

interface Exception
{
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

class RuntimeException extends \RuntimeException implements Exception
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
