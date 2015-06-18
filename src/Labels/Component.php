<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 4.5.14
 * Time: 23:53
 */

namespace Trejjam\Utils\Labels;

use Nette\Application\UI;

class Component extends UI\Control
{
	/**
	 * @var Labels
	 */
	private $labels;

	function setup(Labels $labels)
	{
		$this->labels = $labels;

		return $this;
	}

	public function render($key, $namespace = NULL)
	{
		echo $this->labels->getData($key, $namespace);
	}
}
