<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 7.9.15
 * Time: 15:01
 */

namespace Trejjam\Utils\Components;

use Nette,
	Trejjam;

interface IRenderable
{
	/**
	 * @param null $parameter
	 * @return Nette\Utils\Html
	 */
	function render($parameter = NULL);
}
