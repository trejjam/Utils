<?php

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Md5
{
	/**
	 * @param mixed $input
	 *
	 * @return string
	 * @throws Nette\Utils\JsonException
	 */
	public function filter($input)
	{
		return md5($input);
	}
}
