<?php

namespace Trejjam\Utils\Helpers;

use Tracy;

class TempDownloadFile
{
	private $fileName;

	/**
	 * TempDownloadFile constructor.
	 *
	 * @param string $fileName
	 * @param bool   $removeAfterSend
	 * @param int    $chmod
	 */
	public function __construct($fileName, $removeAfterSend = FALSE, $chmod = 0764)
	{
		$this->fileName = $fileName;
		@chmod($this->fileName, $chmod);
		$this->removeAfterSend = $removeAfterSend;
	}

	public function __toString()
	{
		return $this->fileName;
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function halt()
	{
		if ($this->removeAfterSend) {
			Tracy\Debugger::getLogger()->log('Remove file after send: ' . $this->fileName);
			@unlink($this->fileName);
		}
	}

	public function __destruct()
	{
		$this->halt();
	}
}
