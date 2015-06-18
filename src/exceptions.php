<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 18.6.15
 * Time: 12:17
 */

namespace Trejjam\Utils;


use Nette,
	App,
	Trejjam;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface Exception
{
}

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class InvalidStateException extends \RuntimeException implements Exception
{
}
