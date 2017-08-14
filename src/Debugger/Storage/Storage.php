<?php
declare(strict_types=1);

namespace Trejjam\Utils\Debugger\Storage;

use Efpa;
use GuzzleHttp;
use MicrosoftAzure;

class Storage implements IStorage
{
	/**
	 * @var Efpa\Azure\Blob\ServiceClient
	 */
	public $serviceClient;
	/**
	 * @var string
	 */
	public $blobPrefix;
	/**
	 * @var string
	 */
	protected $containerName;

	public function __construct(Efpa\Azure\Blob\ServiceClient $serviceClient, $blobPrefix)
	{
		$this->serviceClient = $serviceClient;
		$this->blobPrefix = $blobPrefix;
		$this->containerName = IStorage::CONTAINER_MASTER_NAME;
	}

	public function persist(string $localFile) : bool
	{
		$getBlobResult = $this->serviceClient->putBlob(
			$this->getContainerPrefix() . $this->containerName . '-' . IStorage::TYPE_LOG,
			basename($localFile),
			$localFile
		);

		return $getBlobResult instanceof MicrosoftAzure\Storage\Blob\Models\GetBlobResult;
	}

	public function fetch(string $file) : GuzzleHttp\Promise\PromiseInterface
	{
		return $this->serviceClient->getBlobPromise(
			$this->getContainerPrefix() . $this->containerName . '-' . IStorage::TYPE_LOG,
			$file
		)->then(function (MicrosoftAzure\Storage\Blob\Models\GetBlobResult $blobResult) {
			return $blobResult->getContentStream();
		});
	}

	private function getContainerPrefix()
	{
		if (empty($this->blobPrefix)) {
			return '';
		}

		return $this->blobPrefix . '-';
	}
}
