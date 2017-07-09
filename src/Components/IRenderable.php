<?php

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
	function render($parameter = NULL, $list = []);
}
