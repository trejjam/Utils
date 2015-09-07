<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 18:28
 */

namespace Trejjam\Utils\Components;


use Nette,
	Trejjam,
	Nette\Application\UI;

/**
 * Class ListingFactory
 * @persistent(filter)
 */
class ListingFactory extends UI\Control
{
	public $defaultSort = [];
	public $sort        = [];
	public $enabledSort = [
		'asc'  => TRUE,
		'desc' => FALSE,
	];
	public $filter      = [];

	public $columns;
	public $columnsHead;
	/**
	 * @var callback[]
	 */
	public $actionButtons = [];

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

	function __construct($templateFile = NULL, IFilterFactory $filterFactory)
	{
		parent::__construct();

		$this->setTemplate($templateFile);
		$this->filterFactory = $filterFactory;
	}

	public function setTemplate($templateFile)
	{
		$this->templateFile = $templateFile;
	}

	public function setModel(Trejjam\Utils\Helpers\IBaseList $list)
	{
		$this->list = $list;
	}

	public function render()
	{
		$template = $this->createTemplate();
		$template->setFile($this->templateFile);

		/** @var FilterFactory $filter */
		$filter = $this->getComponent('filter');

		$template->listData = $this->list->getList($filter->getSort(), $filter->getFilter(), $filter->getLimit(), ($filter->getPage() - 1) * $filter->getLimit());
		$template->sort = $this->sort;
		$template->filter = $this->filter;
		$template->columns = $this->columns;
		$template->columnsHead = $this->columnsHead;
		$template->actionButtons = $this->actionButtons;

		$template->render();
	}

	public function createComponentFilter()
	{
		$filter = $this->filterFactory->create();

		$filter->setSort($this->sort, $this->enabledSort);
		$filter->defaultSort($this->defaultSort);

		$filter->setFilter($this->filter);

		$filter->countCallback = function ($filter) {
			return $this->list->getCount($filter);
		};

		return $filter;
	}
}
