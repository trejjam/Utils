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

class BaseLayout
{
	/**
	 * @var Nette\Security\User
	 */
	protected $user;
	/**
	 * @var Trejjam\Utils\Labels
	 */
	protected $labels;

	/**
	 * @var array
	 */
	protected $cacheParams = [];

	protected $configurations = [];

	function __construct(Trejjam\Utils\Labels $labels = NULL, Nette\Security\User $user)
	{
		$this->labels = $labels;
		$this->user = $user;
	}

	function setConfigurations(array $configurations)
	{
		$this->configurations = $configurations;
	}
	function getConfigurations()
	{
		return $this->configurations;
	}

	function getLabels()
	{
		return $this->labels;
	}

	public function editFlashes(Nette\Application\UI\ITemplate $template)
	{
		if (!$this->getConfigurations()['flashes']['enable']) {
			return;
		}

		foreach ($template->flashes as $k => $v) {
			if (!isset($template->flashes[$k]->message)) {
				continue;
			}

			$template->flashes[$k]->text = $template->flashes[$k]->message;
			unset($template->flashes[$k]->message);

			try {
				$flashTypeJson = Nette\Utils\Json::decode($v->type);

				unset($template->flashes[$k]->type);
				foreach ($flashTypeJson as $k2 => $v2) {
					$template->flashes[$k]->$k2 = $v2;
				}
			}
			catch (Nette\Utils\JsonException $e) {

			}
		}
	}

	public function registerComponents(Nette\Application\UI\Presenter $presenter)
	{
		if (!$this->getConfigurations()['labels']['enable']) {
			return;
		}

		$presenter->addComponent($this->labels->create(), $this->getConfigurations()['labels']['componentName']);
	}
}

trait BaseLayoutTrait
{
	/**
	 * @var \Trejjam\Utils\Layout\BaseLayout @inject
	 */
	public $layout;
	/**
	 * @var \Trejjam\Utils\Labels
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
