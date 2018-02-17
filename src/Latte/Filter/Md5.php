<?php
declare(strict_types=1);

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Md5
{
	public function filter(string $input) : string
	{
		return md5($input);
	}
}
