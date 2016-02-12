<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 5.1.16
 * Time: 21:03
 */

namespace Trejjam\Utils\Helpers;

use EFPA;

abstract class AShellExecute
{
	/**
	 * @var callable[]
	 */
	public $logger = [];
	/**
	 * @var string
	 */
	protected $loggerName = '';

	public function __construct()
	{
		$this->loggerName = get_called_class();
	}

	/**
	 * @return string
	 */
	public function getLoggerName()
	{
		return $this->loggerName;
	}

	protected function execute($command)
	{
		$time = $timerName = '';
		if (class_exists('\Tracy\Debugger')) {
			\Tracy\Debugger::timer($timerName = md5($command));
		}
		$this->logCmd($command, 'Executing');

		$output = shell_exec($command);

		if (class_exists('\Tracy\Debugger')) {
			$time = \Tracy\Debugger::timer($timerName);
		}
		$this->logCmd($output, 'Result', $time);

		return $output;
	}

	protected function logCmd($message, $title = NULL, $time = NULL)
	{
		foreach ($this->logger as $v) {
			$v($this, $message, $title, $time);
		}
	}
}
