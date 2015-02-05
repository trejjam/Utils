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
	protected $config = [];
	/**
	 * @var array
	 */
	protected $cacheParams = [];

	function __construct(Nette\Security\User $user, Trejjam\Utils\Labels $labels, Trejjam\Utils\PageInfo $pageInfo) {
		$this->user = $user;
		$this->labels = $labels;
		$this->pageInfo = $pageInfo;
	}

	function setConfig(array $config) {
		$this->config = $config;
	}
	function getConfig() {
		return $this->config;
	}
	public function setCacheParams($cacheParams) {
		$this->cacheParams = $cacheParams;
	}

	function setTemplate(Nette\Application\UI\ITemplate $template, $pageInfo) {
		$template->fv = $this->config["fileVersion"];
		$template->debug = $this->config["debugMode"];

		$template->server = $_SERVER['SERVER_NAME'];
		$template->uri = explode("?", isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")[0];

		$this->setBrowserHead($template);
		$template->pageInfo = $pageInfo;

		$template->isUserLogged = $this->user->isLoggedIn();
	}

	private function setBrowserHead(Nette\Application\UI\ITemplate $template) {
		$browser = new \Browser();
		$template->browser = Nette\Utils\Strings::webalize($browser->getBrowser());
		$template->IEversion = ($template->browser == "internet-explorer") ? Nette\Utils\Strings::webalize($browser->getVersion()) : "-1";
	}
}

trait BaseLayoutTrait
{
	/**
	 * @var \Trejjam\Utils\Layout\BaseLayout @inject
	 */
	public $layout;
	/**
	 * @var \Trejjam\Utils\PageInfo @inject
	 */
	public $pageInfo;
	/**
	 * @var \Trejjam\Utils\Labels @inject
	 */
	public $labels;

	private $crumbs = [];

	/**
	 * @var array
	 */
	protected $config;

	function startup() {
		parent::startup();

		$this->config = $this->layout->getConfig();

		if (method_exists($this, 'ownStartup')) {
			$this->ownStartup();
		}
	}
	function beforeRender() {
		$this->layout->setTemplate($this->template, $this->pageInfo->getHead($this->pageInfo->selectPage($this->request)));

		if (method_exists($this, 'ownBeforeRender')) {
			$this->ownBeforeRender();
		}
	}
	function afterRender() {
		parent::afterRender();

		if ($this->config["reformatFlash"]) {
			$this->editFlashs();
		}
		$this->template->crumbs = $this->crumbs;

		if (method_exists($this, 'ownAfterRender')) {
			$this->ownAfterRender();
		}
	}

	function addCrumb($text, $url) {
		$this->crumbs[] = (object)array("text" => $text, "url" => $url);
	}
	function editFlashs() {
		foreach ($this->template->flashes as $k => $v) {
			if (!isset($this->template->flashes[$k]->message)) {
				continue;
			}
			$this->template->flashes[$k]->text = $this->template->flashes[$k]->message;
			unset($this->template->flashes[$k]->message);

			if (\Trejjam\Utils\Utils::isJson($v->type)) {
				$flashTypeJson = json_decode($v->type);

				unset($this->template->flashes[$k]->type);
				foreach ($flashTypeJson as $k2 => $v2) {
					$this->template->flashes[$k]->$k2 = $v2;
				}
			}
		}
	}
	function createComponentLabel() {
		return $this->labels->create();
	}
}
