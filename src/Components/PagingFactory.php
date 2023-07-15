<?php

namespace Trejjam\Utils\Components;

use Trejjam;
use Nette\Application\UI;

/**
 * @method pageCallback
 * @method countCallback
 * @method linkCallback($page)
 */
class PagingFactory extends UI\Control
{
    protected int $spacePage = 2;
    protected int $continuousPage = 4;

    /**
     * @var callable
     */
    public $pageCallback = NULL;
    /**
     * @var callable
     */
    public $countCallback = NULL;
    /**
     * @var callable
     */
    public $linkCallback = NULL;

    function __construct(
        private readonly string|null $templateFile
    )
    {
    }

    public function setSpacePage(int $spacePage): self
    {
        $this->spacePage = $spacePage;

        return $this;
    }

    public function setContinuousPage(int $continuousPage): self
    {
        $this->continuousPage = $continuousPage;

        return $this;
    }

    public function render()
    {
        $template = $this->createTemplate();

        $template->setFile($this->templateFile);

        if (!is_callable($this->pageCallback)) {
            throw new \LogicException('Missing page callback');
        }
        if (!is_callable($this->countCallback)) {
            throw new \LogicException('Missing count callback');
        }

        $template->page = $page = call_user_func($this->pageCallback);
        $template->count = $count = call_user_func($this->countCallback);

        if ($count < 2) {
            return;
        }

        $downContinuous = $page - ceil($this->continuousPage / 2);
        if ($downContinuous < 2) {
            $downContinuous = 1;
        }
        $upContinuous = $page + ceil($this->continuousPage / 2);
        if ($upContinuous > $count) {
            $upContinuous = $count;
        }

        $paging = [];

        $paging[1] = TRUE;

        if ($downContinuous > ($this->spacePage + 1)) {
            $step = ceil($downContinuous / ($this->spacePage + 1));
            for ($i = 1; $i < $downContinuous; $i += $step) {
                $paging[$i] = TRUE;
            }
        }

        for ($i = $downContinuous; $i < $upContinuous; $i++) {
            if ($i > 1) {
                $paging[$i] = TRUE;
            }
        }

        if (($count - $upContinuous) > ($this->spacePage + 1)) {
            $step = ceil(($count - $upContinuous) / ($this->spacePage + 1));
            for ($i = $upContinuous; $i < $count; $i += $step) {
                $paging[$i] = TRUE;
            }
        }

        $paging[$count] = TRUE;

        $previous = NULL;
        foreach ($paging as $k => $v) {
            if (!is_null($previous) && $previous < ($k - 1)) {
                $paging[$k - 1] = (object)[
                    'hasLink' => FALSE,
                    'text' => '&hellip;',
                ];
            }

            $paging[$k] = $this->createLink($k);
            $previous = $k;
        }

        ksort($paging);

        $template->paging = $paging;

        if ($page > 1) {
            $template->previous = $this->createLink($page - 1);
        }
        if ($page < $count) {
            $template->next = $this->createLink($page + 1);
        }

        $template->render();
    }

    protected function createLink($page)
    {
        if (!is_callable($this->linkCallback)) {
            throw new \LogicException('Missing link creating callback');
        }

        return (object)[
            'hasLink' => TRUE,
            'text' => $page,
            'link' => call_user_func($this->linkCallback, $page),
        ];
    }
}
