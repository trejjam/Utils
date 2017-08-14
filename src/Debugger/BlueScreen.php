<?php
declare(strict_types=1);

namespace Trejjam\Utils\Debugger;

use Tracy;
use Trejjam;

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
	 * @var Trejjam\Utils\Debugger\Storage\IStorage
	 */
	protected $storage;

	/**
	 * @param Trejjam\Utils\Debugger\Storage\IStorage|NULL $storage
	 */
	public function setLogStorage(Trejjam\Utils\Debugger\Storage\IStorage $storage = NULL)
	{
		$this->storage = $storage;
	}

	/**
	 * @param array   $sslAuthorizedDn
	 * @param array   $logIgnoreEmail
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

	/**
	 * Renders blue screen to file (if file exists, it will not be overwritten).
	 * @param  \Exception|\Throwable
	 * @param  string file path
	 * @return void
	 */
	public function renderToFile($exception, $file)
	{
		parent::renderToFile($exception, $file);

		if (!is_null($this->storage)) {
			$this->storage->persist($file);
			unlink($file);
		}
	}
}
