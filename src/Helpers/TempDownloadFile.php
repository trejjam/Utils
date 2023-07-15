<?php
declare(strict_types=1);

namespace Trejjam\Utils\Helpers;

use Tracy;

class TempDownloadFile
{
	public function __construct(
		private readonly string $fileName,
		private readonly bool $removeAfterSend = FALSE,
		int $chmod = 0764
	) {
		@chmod($this->fileName, $chmod);
	}

	public function __toString() : string
	{
		return $this->fileName;
	}

	public function getFileName() : string
	{
		return $this->fileName;
	}

	public function halt() : void
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
