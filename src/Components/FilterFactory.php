<?php

namespace Trejjam\Utils\Components;


use Nette;
use Trejjam;
use Nette\Application\UI;

class FilterFactory extends UI\Control
{
    const DEFAULT_LIMIT = 20;

    public array $sort = [];
    public array $sortDbTranslate = [];
    protected array $cleanSortKeys = [];
    public array $filter = [];
    public array $filterDbTranslate = [];

    public int $limit = self::DEFAULT_LIMIT;

    protected int $count;
    public int $page = 1;

    protected array $defaultFilter = [];
    protected array $strictFilter = [];
    protected array $defaultSort = [];
    protected array $enabledSort = [];
    protected array $enableValues = [];

    protected array $enableFilter = [];
    protected array $filterSpecialInput = [];

    /**
     * @var callable
     */
    public $countCallback = NULL;

    function __construct(
        private readonly string|null    $templateFile,
        private readonly IPagingFactory $pagingFactory
    )
    {
        parent::__construct();
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

        if (count($this->sort) == 0) {
            foreach ($this->defaultSort as $k => $v) {
                if (!isset($this->sort[$k])) {
                    $this->sort[$k] = $v;
                }
            }
        }

        foreach ($this->sort as $k => $v) {
            if (!in_array($k, $this->enabledSort) && !isset($this->defaultSort[$k])) {
                unset($this->sort[$k]);
                continue;
            }

            if (!in_array($v, array_keys($this->enableValues))) {
                unset($this->sort[$k]);
                continue;
            }
        }

        foreach ($this->defaultFilter as $k => $v) {
            if (!isset($this->filter[$k])) {
                $this->filter[$k] = $v;
            }
        }
        foreach ($this->filter as $k => $v) {
            if (!in_array($k, $this->enableFilter)) {
                unset($this->filter[$k]);
                continue;
            }
        }

        if (!is_null($this->countCallback)) {
            $this->count = call_user_func($this->countCallback, $this->getDbFilter());
        } else {
            throw new \LogicException('Missing count callback');
        }

        $this->limit = Nette\Utils\Validators::isNumericInt($this->limit) ? $this->limit : static::DEFAULT_LIMIT;
        $this->page = (
            Nette\Utils\Validators::isNumericInt($this->page)
            && $this->page <= ceil($this->count / $this->limit)
            && $this->page > 0
        )
            ? $this->page
            : 1;

        $this->cleanSortKeys = $this->defaultSort;
        foreach ($this->enabledSort as $k => $v) {
            if (isset($this->cleanSortKeys[$k])) {
                unset($this->cleanSortKeys[$k]);
            }
        }
    }

    public function setSort(array $enableSort, array $enableValues): self
    {
        $this->enabledSort = $enableSort;
        $this->enableValues = $enableValues;

        return $this;
    }

    public function setSortDbTranslate(array $sortDbTranslate): self
    {
        $this->sortDbTranslate = $sortDbTranslate;

        return $this;
    }

    public function setDefaultSort(array $defaultSort): self
    {
        $this->defaultSort = $defaultSort;

        return $this;
    }

    public function isSort($name): bool
    {
        return isset($this->sort[$name]);
    }

    public function sort($name, $boolean = FALSE)
    {
        if (!$this->isSort($name)) {
            return NULL;
        } else {
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

        foreach ($this->cleanSortKeys as $k => $v) {
            if (isset($sort[$k])) {
                unset($sort[$k]);
            }
        }

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

    public function getDbSort()
    {
        $out = [];

        foreach ($this->sort as $k => $v) {
            $out[isset($this->sortDbTranslate[$k]) ? $this->sortDbTranslate[$k] : $k] = $v;
        }

        return $out;
    }

    public function setFilter(array $enableFilter): self
    {
        $this->enableFilter = $enableFilter;

        return $this;
    }

    public function setDefaultFilter(array $defaultFilter): self
    {
        $this->defaultFilter = $defaultFilter;

        return $this;
    }

    public function setStrictFilter(array $strictFilter): self
    {
        $this->strictFilter = $strictFilter;

        return $this;
    }

    public function setFilterSpecialInput(array $filterSpecialInput): self
    {
        $this->filterSpecialInput = $filterSpecialInput;

        return $this;
    }

    public function createComponentForm(): UI\Form
    {
        $form = new UI\Form;

        foreach ($this->enableFilter as $fieldName) {
            if (
                array_key_exists($fieldName, $this->filterSpecialInput)
                && array_key_exists('type', $this->filterSpecialInput[$fieldName])
            ) {
                /** @var Nette\Forms\Controls\BaseControl $input */
                $input = call_user_func([$form, $this->filterSpecialInput[$fieldName]['type']], $fieldName);
            } else if (
                array_key_exists($fieldName, $this->filterSpecialInput)
                && array_key_exists('factory', $this->filterSpecialInput[$fieldName])
                && is_callable($this->filterSpecialInput[$fieldName]['factory'])
            ) {
                /** @var Nette\Forms\Controls\BaseControl $input */
                $input = call_user_func($this->filterSpecialInput[$fieldName]['factory'], $this, $form, $fieldName);
            } else {
                /** @var Nette\Forms\Controls\BaseControl $input */
                $input = $form->addText($fieldName);
            }
            if (array_key_exists($fieldName, $this->filter)) {
                $input->setDefaultValue($this->filter[$fieldName]);
            }

            if (
                array_key_exists($fieldName, $this->filterSpecialInput)
                && array_key_exists('onValidate', $this->filterSpecialInput[$fieldName])
                && is_callable($this->filterSpecialInput[$fieldName]['onValidate'])
            ) {
                $form->onValidate[] = function (Nette\Forms\Form $form) use ($fieldName, $input) {
                    call_user_func($this->filterSpecialInput[$fieldName]['onValidate'], $form, $input);
                };
            }
        }

        $form->addSubmit('send', 'Filter');
        $form->onSuccess[] = [$this, 'updateFilter'];

        return $form;
    }

    public function updateFilter(UI\Form $form)
    {
        $values = $form->getValues();

        $filter = [];
        foreach ($values as $k => $v) {
            if ($k === 'send' || $v === '') {
                continue;
            }

            $filter[$k] = $v;
        }

        $this->redirect('this', ['filter' => $filter]);
    }

    public function getFilter()
    {
        return array_merge([Trejjam\Utils\Helpers\Database\ABaseList::STRICT => $this->strictFilter], $this->filter);
    }

    public function getDbFilter()
    {
        return $this->getFilter();
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function createComponentPaging(): PagingFactory
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
