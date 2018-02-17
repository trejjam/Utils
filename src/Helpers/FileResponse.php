<?php
declare(strict_types=1);

namespace Trejjam\Utils\Helpers;

use Nette;

class FileResponse extends Nette\Application\Responses\FileResponse
{
	/**
	 * @var string|TempDownloadFile
	 */
	protected $file;

	public function __construct(
		$file,
		string $name = NULL,
		string $contentType = NULL,
		bool $forceDownload = TRUE
	) {
		$this->file = $file;
		$filename = ($file instanceof TempDownloadFile && is_file($file->getFileName()))
			? $file->getFileName()
			: $file;

		parent::__construct($filename, $name, $contentType, $forceDownload);
	}

	public function send(
		Nette\Http\IRequest $httpRequest,
		Nette\Http\IResponse $httpResponse
	) {
		$httpResponse->setExpiration(FALSE);
		parent::send($httpRequest, $httpResponse);

		if ($this->file instanceof TempDownloadFile && is_file($this->file->getFileName())) {
			$this->file->halt();
		}
	}
}
