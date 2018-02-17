<?php
declare(strict_types=1);

namespace Trejjam\Utils\Components;

use Nette;

interface IRenderable
{
	/**
	 * @param \stdClass|null $parameter
	 * @param \stdClass[]    $list
	 *
	 * @return Nette\Utils\Html
	 */
	function render($parameter = NULL, array $list = []) : Nette\Utils\Html;
}
