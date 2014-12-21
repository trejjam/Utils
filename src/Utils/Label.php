<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 4.5.14
 * Time: 23:53
 */

namespace Trejjam\Utils\Components;

use Nette\Application\UI;

class Label extends \Nette\Application\UI\Control
{
	/**
	 * @var \Trejjam\Utils\Labels
	 */
	private $labels;

	function setup(\Trejjam\Utils\Labels $labels) {
		$this->labels = $labels;

		return $this;
	}

	public function render($key, $namespace = NULL) {
		echo $this->labels->getData($key, $namespace);
	}
}