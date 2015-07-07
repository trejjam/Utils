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
