<?php

namespace Trejjam\Utils\Debugger;

use Nette;
use Tracy;

class BlueScreen extends Tracy\BlueScreen
{
	/**
	 * @var string
	 */
	protected $siteMode;
	/**
	 * @var array
	 */
	protected $sslAuthorizedDn;
	/**
	 * @var array
	 */
	protected $logIgnoreEmail;

	/**
	 * @param array $sslAuthorizedDn
	 * @param array $logIgnoreEmail
	 */
	public function setSslAuthorizedDn($sslAuthorizedDn, $logIgnoreEmail)
	{
		$this->sslAuthorizedDn = $sslAuthorizedDn;
		$this->logIgnoreEmail = $logIgnoreEmail;
	}

	/**
	 * @param string $siteMode
	 */
	public function setSiteMode($siteMode)
	{
		$this->siteMode = $siteMode;
	}

	/**
	 * Renders blue screen.
	 *
	 * @param  \Exception|\Throwable
	 *
	 * @return void
	 */
	public function render($exception)
	{
		if (
			(
				isset($this->sslAuthorizedDn['emailAddress'])
				&& !in_array($this->sslAuthorizedDn['emailAddress'], $this->logIgnoreEmail)
			)
			&& in_array($this->siteMode, [
				'public',
				'ssd',
			])
		) {
			if ($exception instanceof \ErrorException) {
				$severity = $exception->getSeverity();
				Debugger::getLogger()
						->log($exception,
							  ($severity & Debugger::$logSeverity) === $severity
								  ? Tracy\ILogger::ERROR
								  : Tracy\ILogger::EXCEPTION
						);
			}
			else {
				Debugger::getLogger()->log($exception, Tracy\ILogger::EXCEPTION);
			}
		}

		parent::render($exception);
	}
}
