<?php
declare(strict_types=1);

namespace Trejjam\Utils\Components;

use Nette;

class HtmlButton implements IRenderable
{
	/**
	 * @var callback($parameter)
	 */
	protected $onRender = NULL;

	public function __construct(callable $onRender)
	{
		$this->onRender = $onRender;
	}

	/**
	 * @inheritdoc
	 */
	function render($parameter = NULL, array $list = []) : Nette\Utils\Html
	{
		return call_user_func($this->onRender, $parameter);
	}
}
