<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 1.3.15
 * Time: 3:12
 */

namespace Trejjam\Utils\Components;


use Nette,
	Trejjam,
	Nette\Application\UI;

class FilterFactory extends UI\Control
{
	const
		DEFAULT_LIMIT = 20;

	/**
	 * @var string
	 */
	protected $templateFile;
	/**
	 * @var Trejjam\Utils\Components\IPagingFactory
	 */
	protected $pagingFactory;

	/**
	 * @var array
	 */
	public $sort = [];
	/**
	 * @var array
	 */
	public $filter = [];
	/**
	 * @var int
	 */
	public $limit = self::DEFAULT_LIMIT;
	/**
	 * @var int
	 */
	protected $count;
	/**
	 * @var int
	 */
	public $page = 1;

	protected $defaultSort  = [];
	protected $enabledSort  = [];
	protected $enableValues = [];

	protected $enableFilter = [];

	/**
	 * @var callable
	 */
	public $countCallback = NULL;

	function __construct($templateFile = NULL, IPagingFactory $pagingComponent)
	{
		parent::__construct();

		$this->templateFile = $templateFile;
		$this->pagingFactory = $pagingComponent;
	}

	public static function getPersistentParams()
	{
		return [
			'sort',
			'filter',
			'page',
		];
	}

	function loadState(array $params)
	{
		parent::loadState($params);

		foreach ($this->defaultSort as $k => $v) {
			if (!isset($this->sort[$k])) {
				$this->sort[$k] = $v;
			}
		}

		foreach ($this->sort as $k => $v) {
			if (!in_array($k, $this->enabledSort)) {
				unset($this->sort[$k]);
				continue;
			}

			if (!in_array($v, array_keys($this->enableValues))) {
				unset($this->sort[$k]);
				continue;
			}
		}

		foreach ($this->filter as $k => $v) {
			if (!in_array($k, $this->enableFilter)) {
				unset($this->filter[$k]);
				continue;
			}
		}

		if (!is_null($this->countCallback)) {
			$this->count = $this->countCallback($this->filter);
		}
		else {
			throw new \LogicException('Missing count callback');
		}

		$this->limit = Nette\Utils\Validators::isNumericInt($this->limit) ? $this->limit : self::DEFAULT_LIMIT;
		$this->page = (Nette\Utils\Validators::isNumericInt($this->page) && $this->page <= ceil($this->count / $this->limit) && $this->page > 0) ? $this->page : 1;
	}

	public function setSort(array $enableSort, array $enableValues)
	{
		$this->enabledSort = $enableSort;
		$this->enableValues = $enableValues;
	}

	public function defaultSort(array $defaultSort)
	{
		$this->defaultSort = $defaultSort;
	}

	public function isSort($name)
	{
		return isset($this->sort[$name]);
	}
	public function sort($name, $boolean = FALSE)
	{
		if (!$this->isSort($name)) {
			return NULL;
		}
		else {
			return $boolean ? $this->enableValues[$this->sort[$name]] : $this->sort[$name];
		}
	}
	public function getNextSort($name)
	{
		foreach ($this->enableValues as $k => $v) {
			if (isset($this->sort[$name]) && $this->sort[$name] == $k) {
				continue;
			}

			return $k;
		}

		return '';
	}

	public function getNextArr($name)
	{
		$sort = $this->sort;

		$sort[$name] = $this->getNextSort($name);

		return $sort;
	}

	public function renderSortLink($key, $name)
	{
		$template = $this->createTemplate();
		$template->setFile($this->templateFile);

		$template->filter = $this;
		$template->key = $key;
		$template->name = $name;

		$template->render();
	}

	public function getSort()
	{
		return $this->sort;
	}

	public function setFilter(array $enableFilter)
	{
		$this->enableFilter = $enableFilter;
	}

	public function createComponentForm()
	{
		$form = new UI\Form;

		foreach ($this->enableFilter as $v) {
			$input = $form->addText($v);
			if (isset($this->filter[$v])) {
				$input->setDefaultValue($this->filter[$v]);
			}
		}

		$form->addSubmit('send', 'Filter');
		$form->onSuccess[] = $this->updateFilter;

		return $form;
	}

	public function updateFilter(UI\Form $form)
	{
		$values = $form->getValues();

		$filter = [];
		foreach ($values as $k => $v) {
			if ($k == 'send' || $v == '') {
				continue;
			}

			$filter[$k] = $v;
		}

		$this->redirect('this', ['filter' => $filter]);
	}

	public function getFilter()
	{
		return $this->filter;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function getPage()
	{
		return $this->page;
	}

	public function createComponentPaging()
	{
		$paging = $this->pagingFactory->create();

		$paging->pageCallback = function () {
			return $this->page;
		};
		$paging->countCallback = function () {
			return ceil($this->count / $this->limit);
		};
		$paging->linkCallback = function ($page) {
			return $this->link('this', ['page' => $page]);
		};

		return $paging;
	}
}
