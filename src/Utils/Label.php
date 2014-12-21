<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 4.5.14
 * Time: 23:53
 */

namespace Trejjam\Utils\Components;

class Label extends \Nette\Application\UI\Control
{
	/**
	 * @var \Trejjam\Utils\Labels
	 */
	private $labels;

	function setup(\Trejjam\Utils\Labels $labels) {
		$this->labels=$labels;
	}

	public function render($key)
	{
		echo $this->labelService->$key;
	}
}

interface ILabelFactory
{
	/**
	 * @return Label
	 */
	function create();
}