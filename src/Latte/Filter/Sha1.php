<?php

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Sha1
{
	/**
	 * @param mixed $input
	 *
	 * @return string
	 * @throws Nette\Utils\JsonException
	 */
	public function filter($input)
	{
		return sha1($input);
	}
}
