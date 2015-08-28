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

class PagingFactory extends UI\Control
{
	protected $spacePage      = 2;
	protected $continuousPage = 4;

	/**
	 * @var string
	 */
	protected $templateFile;

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

	function __construct($templateFile = NULL)
	{
		$this->templateFile = $templateFile;
	}

	public function render()
	{
		$template = $this->createTemplate();

		$template->setFile($this->templateFile);

		if (is_null($this->pageCallback)) {
			throw new \LogicException('Missing page callback');
		}
		if (is_null($this->countCallback)) {
			throw new \LogicException('Missing count callback');
		}

		$template->page = $page = $this->pageCallback();
		$template->count = $count = $this->countCallback();

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
					'text'    => '&hellip;',
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
		if (is_null($this->linkCallback)) {
			throw new \LogicException('Missing link creating callback');
		}

		return (object)[
			'hasLink' => TRUE,
			'text'    => $page,
			'link'    => $this->linkCallback($page),
		];
	}
}
