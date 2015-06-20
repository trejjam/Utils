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
		CONTENTS_UNKNOWN_ITEM_TYPE = 1,
		CONTENTS_INCOMPLETE_CONFIGURATION = 2,
		CONTENTS_COLLISION_CONFIGURATION = 4,
		CONTENTS_MISSING_CONFIGURATION = 8,
		CONTENTS_JSON_DECODE = 16;
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
