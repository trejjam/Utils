<?php
declare(strict_types=1);

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Json
{
	/**
	 * @param mixed      $input
	 * @param int|string $options
	 *
	 * @return string
	 * @throws Nette\Utils\JsonException
	 */
	public function filter($input, $options = 0) : string
	{
		if (is_string($options)) {
			$options = constant(Nette\Utils\Json::class . '::' . strtoupper($options));
		}

		return Nette\Utils\Json::encode($input, $options);
	}
}
