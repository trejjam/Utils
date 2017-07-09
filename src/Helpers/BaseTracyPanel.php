<?php

namespace Trejjam\Utils\Helpers;

use Tracy;
use Trejjam;

/**
 * Base debug panel.
 */
class BaseTracyPanel implements Tracy\IBarPanel
{
	protected $count = 0;

	/**
	 * @var array
	 */
	protected $cmd = [];

	/**
	 * @var string
	 */
	public $name;

	public $disabled = FALSE;

	/**
	 * @var string
	 */
	protected $tabTemplate = '';
	/**
	 * @var string
	 */
	protected $panelTemplate = '';

	public function __construct(AShellExecute $execute, $tabTemplate = NULL, $panelTemplate = NULL)
	{
		$execute->logger[] = [$this, 'logCmd'];
		$this->name = $execute->getLoggerName();

		if ( !is_null($tabTemplate)) {
			$this->tabTemplate = $tabTemplate;
		}
		if ( !is_null($panelTemplate)) {
			$this->panelTemplate = $panelTemplate;
		}
	}

	public function logCmd(AShellExecute $that, $message, $title, $time)
	{
		if ($this->disabled) {
			return;
		}
		$this->count++;

		$this->cmd[] = [
			'message' => $message,
			'title'   => $title,
			'time'    => $time,
		];
	}

	public function getTab()
	{
		$name = $this->name;
		$count = $this->count;

		if ( !$count) {
			return '';
		}

		ob_start();
		require $this->tabTemplate;

		return ob_get_clean();
	}


	public function getPanel()
	{
		$this->disabled = TRUE;
		if ( !$this->count) {
			return NULL;
		}

		$name = $this->name;
		$count = $this->count;
		$cmd = $this->cmd;

		ob_start();
		require $this->panelTemplate;

		return ob_get_clean();
	}
}
