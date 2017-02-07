<?php

namespace Trejjam\Utils\Helpers;

use Nette;

class FileResponse extends Nette\Application\Responses\FileResponse
{
	/**
	 * @var string|TempDownloadFile
	 */
	protected $file;

	/**
	 * @param string $file        file path
	 * @param string $name        imposed file name
	 * @param string $contentType MIME content type
	 * @param bool   $forceDownload
	 *
	 * @throws Nette\Application\BadRequestException
	 */
	public function __construct($file, $name = NULL, $contentType = NULL, $forceDownload = TRUE)
	{
		$this->file = $file;
		$filename = ($file instanceof TempDownloadFile && is_file((string)$file)) ? (string)$file : $file;
		parent::__construct($filename, $name, $contentType, $forceDownload);
	}

	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setExpiration(FALSE);
		parent::send($httpRequest, $httpResponse);

		if ($this->file instanceof TempDownloadFile && is_file((string)$this->file)) {
			$this->file->halt();
		}
	}
}
