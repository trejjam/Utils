<?php
declare(strict_types=1);

namespace Trejjam\Utils\Latte\Filter;

use Nette;
use Trejjam;

class Sha1
{
	public function filter(string $input) : string
	{
		return sha1($input);
	}
}
