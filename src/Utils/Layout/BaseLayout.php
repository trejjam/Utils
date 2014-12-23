<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 15. 11. 2014
 * Time: 19:33
 */

namespace Trejjam\Utils\Layout;

use Nette;

class BaseLayout
{
	/**
	 * @var Nette\Security\User
	 */
	private $user;
	/**
	 * @var \Trejjam\Utils\Labels
	 */
	private $labels;

	/**
	 * @var array
	 */
	private $config = [];

	function __construct(Nette\Security\User $user, \Trejjam\Utils\Labels $labels, \Trejjam\Utils\PageInfo $pageInfo) {
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

	function setTemplate(Nette\Application\UI\ITemplate $template, $pageInfo) {
		$template->fv = $this->config["fileVersion"];

		$template->server = $_SERVER['SERVER_NAME'];
		$template->uri = explode("?", isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")[0];

		$this->setBrowserHead($template);
		$template->pageInfo = $pageInfo;

		$template->userLogged = $this->user->isLoggedIn();

		$template->jsName = "default";
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

	function beforeRender() {
		$this->layout->setTemplate($this->template, $this->pageInfo->getHead($this->pageInfo->selectPage($this->request)));

		$this->config = $this->layout->getConfig();

		if (method_exists($this, 'ownBeforeRender')) {
			$this->ownBeforeRender();
		}
	}

	function addCrumb($text, $url) {
		$this->crumbs[] = (object)array("text" => $text, "url" => $url);
	}
	function afterRender() {
		parent::afterRender();

		if ($this->config["reformatFlash"]) {
			$this->editFlashs();
		}
		$this->template->crumbs = $this->crumbs;
	}

	function editFlashs() {
		foreach ($this->template->flashes as $k => $v) {
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
	public function getJsCache($name = "default") {
		return isset($this->cache) ? (isset($this->cache["jsCache/" . $name]) ? $this->cache["jsCache/" . $name] . ".js" : "") : "";
	}
	public function getJsFiles($listFile = "default") {
		$out = [];

		$dir = $this->config["wwwDir"] . "/js/";

		if (!is_file($dir . $listFile . ".js-list")) {
			return FALSE;
		}

		$jsList = file('safe://' . $dir . $listFile . ".js-list", FILE_IGNORE_NEW_LINES);
		foreach ($jsList as $v) {
			$out[] = $v;
		}

		return $out;
	}
	function createComponentLabel() {
		return $this->labels->create();
	}
}
