<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 16. 11. 2014
 * Time: 0:19
 */

namespace Trejjam\Utils;

use Nette;

class PageInfo
{
	const
		CACHE_PAGE_LIST = "page_list";

	protected $cacheParams = [
		Nette\Caching\Cache::EXPIRE => '60 minutes'
	];

	/**
	 * @var Nette\Caching\Cache
	 */
	protected $cache;
	/**
	 * @var Nette\Database\Context
	 */
	protected $database;
	/**
	 * @var Labels
	 */
	protected $labels;

	protected $config;

	/**
	 * @var Page[]
	 */
	protected $pages = [];
	/**
	 * @var Page[]
	 */
	protected $rootPages = [];

	protected $generated = NULL;

	function __construct(Nette\Caching\Cache $cache = NULL, Nette\Database\Context $database, Labels $labels) {
		$this->cache = $cache;
		$this->database = $database;
		$this->labels = $labels;
	}
	function setConfig(array $config) {
		$this->config = $config;
	}
	public function setTimeout($timeout) {
		$this->cache[Nette\Caching\Cache::EXPIRE] = $timeout;
	}

	private function useCache() {
		return !is_null($this->cache);
	}
	private function setCache($key, $value) {
		if (!$this->useCache()) return;

		$this->cache->save($key, $value, $this->cacheParams);
	}
	private function getCache($key) {
		if (!$this->useCache()) return NULL;

		if (!is_null(
			$out = $this->cache->load($key)
		)
		) {
			return $out;
		}

		return NULL;
	}

	/**
	 * @return Page[]
	 */
	public function getTree() {
		if (count($this->rootPages) == 0) {
			if (!is_null($value = $this->getCache(self::CACHE_PAGE_LIST))) {
				$this->rootPages = unserialize($value);
				foreach ($this->rootPages as $v) {
					$v->fillArray($this->pages);
				}

			}
			else {
				$this->createTree();
			}
		}

		return $this->rootPages;
	}
	protected function createTree() {
		foreach ($this->database->table($this->config["table"]) as $v) {
			$this->pages[$v->{$this->config["id"]}] = new Page($v, [
				"id"           => $this->config["id"],
				"parentId"     => $this->config["parentId"],
				"page"         => $this->config["page"],
				"subAttribute" => $this->config["subAttribute"],
				"title"        => $this->config["title"],
				"description"  => $this->config["description"],
				"keywords"     => $this->config["keywords"],
				"img"          => $this->config["img"],
			]);
		}

		foreach ($this->pages as $v) {
			$v->connectToParent($this->pages);

			if (!$v->hasParent()) {
				$this->rootPages[$v->getId()] = $v;

				if ($v->getId() != $this->config["rootPage"]) {
					$v->setPseudoParent($this->pages[$this->config["rootPage"]]);
				}
			}
		}

		$this->setCache(self::CACHE_PAGE_LIST, serialize($this->rootPages));
	}

	/**
	 * @param Nette\Application\Request $request
	 * @return Page
	 */
	public function selectPage(Nette\Application\Request $request) {
		$tree = $this->getTree();
		$selected = NULL;
		foreach ($tree as $v) {
			if ($v->getPage() == $request->getPresenterName()) {
				$selected = $v;

				$selected = $selected->findCandidateByParams($request->getParameters());

				break;
			}
		}

		if (is_null($selected)) {
			$selected = $this->pages[$this->config["rootPage"]];
		}

		return $selected;
	}
	/**
	 * @param int $id
	 * @return Page|null
	 */
	public function getPage($id) {
		$this->getTree();
		foreach ($this->pages as $v) {
			if ($v->getId() == $id) {
				return $v;
			}
		}

		return NULL;
	}
	/**
	 * @param string $name
	 * @return Page|null
	 */
	public function getRootPage($name) {
		foreach ($this->pages as $v) {
			if ($v->getPage() == $name) {
				return $v;
			}
		}

		return NULL;
	}
	public function getHead(Page $page) {
		$out = new \stdClass();
		$out->id = $page->getId();
		$out->title = $page->getTitleText() . $this->labels->getData("pageTitle");
		$out->description = $page->getText("description");
		$out->keywords = $page->getText("keywords");
		$out->fbImg = $page->getText("img");

		$this->generated = $out;

		return $out;
	}
	public function getGenerated() {
		return $this->generated;
	}
}

class Page
{
	protected $id;
	protected $parentId;
	protected $page;
	protected $subAttribute;
	protected $title;
	protected $description;
	protected $keywords;
	protected $img;
	/**
	 * @var Page
	 */
	protected $parent;
	/**
	 * @var Page
	 */
	protected $pseudoParent = NULL; //link for root pages to home page
	/**
	 * @var Page[]
	 */
	protected $child = [];

	public function __construct(Nette\Database\Table\IRow $row, array $cells) {
		foreach ($cells as $k => $v) {
			if (is_numeric($k)) $k = $v;
			$this->$k = $row->$v;
		}
	}

	public function setPseudoParent(Page $parent) {
		$this->pseudoParent = $parent;
	}
	/**
	 * @param array $pages
	 */
	public function connectToParent(array $pages) {
		if (!$this->hasParent()) return;
		$this->parent = $pages[$this->parentId];
		$this->parent->connectToChild($this);
	}
	/**
	 * @param Page $page
	 */
	public function connectToChild(Page $page) {
		$this->child[$page->getId()] = $page;
	}
	public function fillArray(array &$page) {
		$page[$this->getId()]=$this;

		foreach ($this->getChild() as $v) {
			$v->fillArray($page);
		}
	}

	/**
	 * @return bool
	 */
	public function hasParent() {
		return !is_null($this->parentId);
	}
	public function hasPseudoParent() {
		return !is_null($this->pseudoParent);
	}

	/**
	 * @param array $params
	 * @return $this|Page
	 */
	public function findCandidateByParams(array $params) {
		if (!is_null($this->subAttribute) && isset($params[$this->subAttribute])) {
			foreach ($this->child as $v) {
				if ($v->getPage() == $params[$this->subAttribute]) {
					return $v->findCandidateByParams($params);
				}
			}
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	/**
	 * @return null|Page
	 */
	public function getParent() {
		if ($this->hasParent()) {
			return $this->parent;
		}

		return NULL;
	}
	/**
	 * @return null|Page
	 */
	public function getPseudoParent() {
		if ($this->hasPseudoParent()) {
			return $this->pseudoParent;
		}

		return NULL;
	}
	/**
	 * @param bool $usePseudo
	 * @return null|Page
	 */
	public function findParent($usePseudo = TRUE) {
		if ($this->hasParent()) {
			return $this->getParent();
		}
		else if ($usePseudo && $this->hasPseudoParent()) {
			return $this->getPseudoParent();
		}

		return NULL;
	}
	/**
	 * @return mixed
	 */
	public function getPage() {
		return $this->page;
	}
	/**
	 * @param string $delimiter
	 * @return string
	 */
	public function getTitleText($delimiter = " - ") {
		$out = "";
		foreach ($this->getRootWay() as $v) {
			if (!is_null($v->getTitle())) {
				$out = $v->getTitle() . $delimiter . $out;
			}
			if ($out == "" && $v->hasPseudoParent() && !is_null($v->getPseudoParent()->getTitle())) {
				$out = $v->getPseudoParent()->getTitle() . $delimiter;
			}
		}

		return $out;
	}
	/**
	 * @param $name
	 * @return string
	 */
	public function getText($name) {
		$get = "get" . Nette\Utils\Strings::firstUpper($name);
		foreach ($this->getRootWay() as $v) {
			if (!is_null($v->$get())) {
				return $v->$get();
			}
			if ($v->hasPseudoParent() && !is_null($v->getPseudoParent()->$get())) {
				return $v->getPseudoParent()->$get();
			}
		}

		return "";
	}

	public function getTitle() {
		return $this->title;
	}
	public function getDescription() {
		return $this->description;
	}
	public function getKeywords() {
		return $this->keywords;
	}
	public function getImg() {
		return $this->img;
	}

	/**
	 * @return Page[]
	 */
	public function getChild() {
		return $this->child;
	}
	/**
	 * @return Page[]
	 */
	public function getRootWay() {
		if (!$this->hasParent()) {
			return [$this];
		}
		else {
			$parentWay = $this->parent->getRootWay();
			$parentWay[] = $this;

			return $parentWay;
		}
	}
}