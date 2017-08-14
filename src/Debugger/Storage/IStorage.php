<?php
declare(strict_types=1);

namespace Trejjam\Utils\Debugger\Storage;

use GuzzleHttp;

interface IStorage
{
	const CONTAINER_MASTER_NAME = 'efpa';
	const HTML_EXT              = '.html';
	const TYPE_LOG              = 'log';

	public function persist(string $localFile) : bool;

	public function fetch(string $file) : GuzzleHttp\Promise\PromiseInterface;
}
