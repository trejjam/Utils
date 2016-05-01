<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 7.9.15
 * Time: 15:08
 */

namespace Trejjam\Utils\Components;

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
	function render($parameter = NULL, $list = [])
	{
		return call_user_func($this->onRender, $parameter);
	}
}
