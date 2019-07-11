<?php
declare(strict_types=1);

namespace Trejjam\Utils\Helpers;

use Nette\Application\IResponse as IApplicationResponse;
use Nette\Application\Responses\FileResponse as NetteFileResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

final class FileResponse implements IApplicationResponse
{
	/**
	 * @var string|TempDownloadFile
	 */
	protected $file;

	/**
	 * @var NetteFileResponse
	 */
	private $innerResponse;

	public function __construct(
		$file,
		string $name = null,
		string $contentType = null,
		bool $forceDownload = true
	) {
		$this->file = $file;
		$filename = ($file instanceof TempDownloadFile && is_file($file->getFileName()))
			? $file->getFileName()
			: $file;

		$this->innerResponse = new NetteFileResponse($filename, $name, $contentType, $forceDownload);
	}

	public function send(IRequest $httpRequest, IResponse $httpResponse) : void
	{
		$httpResponse->setExpiration(null);

		$this->innerResponse->send($httpRequest, $httpResponse);

		if ($this->file instanceof TempDownloadFile && is_file($this->file->getFileName())) {
			$this->file->halt();
		}
	}
}
