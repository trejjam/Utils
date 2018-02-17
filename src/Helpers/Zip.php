<?php
declare(strict_types=1);

namespace Trejjam\Utils\Helpers;

use Trejjam;

class Zip
{
	public static function create(string $zipFileName, array $files) : string
	{
		$zip = new \ZipArchive;

		if ($zip->open($zipFileName, \ZipArchive::CREATE) !== TRUE) {
			throw new Trejjam\Utils\RuntimeException("cannot open '$zipFileName'");
		}

		foreach ($files as $filename => $file) {
			if ( !$zip->addFile($file, $filename)) {
				throw new Trejjam\Utils\RuntimeException("cannot add '$filename'");
			}
		}

		if ( !$zip->close()) {
			throw new Trejjam\Utils\RuntimeException("cannot save '$zipFileName'");
		}

		return $zipFileName;
	}
}
