<?php

namespace Trejjam\Utils\Components;

use Nette;
use Trejjam;
use Nette\Application\UI;

/**
 * Class ListingFactory
 * @persistent(filter)
 */
class ListingFactory extends UI\Control
{
	public $defaultSort        = [];
	public $sort               = [];
	public $sortDbTranslate    = [];
	public $enabledSort        = [
		'asc'  => TRUE,
		'desc' => FALSE,
	];
	public $defaultFilter      = [];
	public $strictFilter       = [];
	public $filterSpecialInput = [];
	public $filter             = [];
	public $filterDbTranslate  = [];
	public $filterCompareType  = [];
	public $multipleFilter     = [];
	public $displayFilters = TRUE;

	public $columns;
	public $columnsHead;
	/**
	 * @var callback[]
	 */
	public $actionButtons = [];

	public $controlVariables = [];

	/**
	 * @var Trejjam\Utils\Helpers\IBaseList
	 */
	protected $list = NULL;

	/**
	 * @var string
	 */
	protected $templateFile;
	/**
	 * @var IFilterFactory
	 */
	protected $filterFactory;

	function __construct($templateFile = NULL, IFilterFactory $filterFactory, Trejjam\Utils\Helpers\IBaseList $list = NULL)
	{
		parent::__construct();

		$this->setTemplate($templateFile);
		$this->filterFactory = $filterFactory;
		$this->list = $list;
	}

	public function setTemplate($templateFile)
	{
		$this->templateFile = $templateFile;
	}

	/**
	 * @param Trejjam\Utils\Helpers\IBaseList $list
	 *
	 * @deprecated use constructor
	 */
	public function setModel(Trejjam\Utils\Helpers\IBaseList $list)
	{
		$this->list = $list;
	}

	public function getModel()
	{
		return $this->list;
	}

	public function render()
	{
		$template = $this->createTemplate();
		$template->setFile($this->templateFile);

		/** @var FilterFactory $filter */
		$template->filterComponent = $filter = $this->getComponent('filter');

		$template->listData = $this->list->getList($filter->getDbSort(), $filter->getDbFilter(), $filter->getLimit(), ($filter->getPage() - 1) * $filter->getLimit(), $this->filterCompareType, $this->filterDbTranslate);
		$template->sort = $this->sort;
		$template->appliedSort = $filter->getSort();
		$template->filter = array_combine($this->filter, $this->filter);
		$template->multipleFilter = $this->multipleFilter;
		$template->appliedFilter = $appliedFilter = $filter->getFilter();
		unset($appliedFilter[Trejjam\Utils\Helpers\Database\ABaseList::STRICT]);
		$template->appliedLikeFilter = $appliedFilter;
		$template->columns = $this->columns;
		$template->columnsHead = $this->columnsHead;
		$template->actionButtons = $this->actionButtons;
		$template->displayFilters = $this->displayFilters;

		$controlCache = [];
		$template->getControl = function ($name) use ($controlCache) {
			if ( !array_key_exists($name, $controlCache)) {
				$controlCache[$name] = $this->controlVariables[$name]();
			}

			return $controlCache[$name];
		};

		$template->render();
	}

	public function createComponentFilter()
	{
		$filter = $this->filterFactory->create();

		$filter->setSort($this->sort, $this->enabledSort)
			   ->setSortDbTranslate($this->sortDbTranslate)
			   ->setDefaultSort($this->defaultSort)
			   ->setFilter($this->filter)
			   //->setFilterDbTranslate($this->filterDbTranslate)
			   ->setDefaultFilter($this->defaultFilter)
			   ->setStrictFilter($this->strictFilter)
			   ->setFilterSpecialInput($this->filterSpecialInput);

		$filter->countCallback = function ($filter) {
			return $this->list->getCount($filter, $this->filterCompareType, $this->filterDbTranslate);
		};

		return $filter;
	}
}
