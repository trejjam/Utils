<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 15. 11. 2014
 * Time: 19:33
 */

namespace Trejjam\Utils\Layout;

use Nette,
	Trejjam;

trait BaseLayoutTrait
{
	/**
	 * @var \Trejjam\Utils\Layout\BaseLayout @inject
	 */
	public $layout;
	/**
	 * @var \Trejjam\Utils\Labels\Labels
	 */
	protected $labels = NULL;

	private $crumbs = [];

	function startup()
	{
		parent::startup();
		$this->labels = $this->layout->getLabels();
		$this->layout->registerComponents($this);

		if (method_exists($this, 'ownStartup')) {
			$this->ownStartup();
		}
	}
	function afterRender()
	{
		parent::afterRender();

		$this->layout->editFlashes($this->template);

		if (method_exists($this, 'ownAfterRender')) {
			$this->ownAfterRender();
		}
	}
}
