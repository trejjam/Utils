<?php

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Json
{
	/**
	 * @param mixed $input
	 *
	 * @return string
	 * @throws Nette\Utils\JsonException
	 */
	public function filter($input)
	{
		return Nette\Utils\Json::encode($input);
	}
}
