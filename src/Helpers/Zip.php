<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 12.2.16
 * Time: 11:28
 */

namespace Trejjam\Utils\Helpers;

use Nette,
	App,
	Trejjam;

class Zip
{
	public static function create($zipFileName, array $files)
	{
		$zip = new \ZipArchive();

		if ($zip->open($zipFileName, \ZipArchive::CREATE) !== TRUE) {
			throw new \RuntimeException("cannot open '$zipFileName'");
		}

		foreach ($files as $filename => $file) {
			if ( !$zip->addFromString($filename, file_get_contents($file))) {
				throw new \RuntimeException("cannot add '$filename'");
			}
		}

		if ( !$zip->close()) {
			throw new \RuntimeException("cannot save '$zipFileName'");
		}

		return $zipFileName;
	}
}
