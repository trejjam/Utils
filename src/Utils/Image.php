<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 23.12.14
 * Time: 17:58
 */

namespace Trejjam\Utils;


use Nette;

class Image
{
	/**
	 * @var array
	 */
	protected $config;

	public function setConfig(array $config) {
		$this->config = $config;
	}
	public function getPath($name) {
		if (isset($this->config["paths"][$name])) {
			return $this->unifiDir($this->config["paths"][$name]);
		}
		throw new ImageException("Paths not found. Did you register it in configuration?", ImageException::PATH_NOT_FOUND);
	}
	/**
	 * @param $name
	 * @return mixed
	 * @throws ImageException
	 */
	public function getRoutine($name) {
		if (isset($this->config["routine"][$name])) {
			foreach ($this->config["routine"][$name] as $k => $v) {
				$this->config["routine"][$name][$k]["dir"] = $this->unifiDir($v["dir"]);
			}


			return $this->config["routine"][$name];
		}
		throw new ImageException("Routine not found. Did you register it in configuration?", ImageException::ROUTINE_NOT_FOUND);
	}
	/**
	 * @param $dir
	 * @return string
	 */
	protected function unifiDir($dir) {
		$dirEnds = $dir[Nette\Utils\Strings::length($dir) - 1];
		$dir .= in_array($dirEnds, ['\\', '/']) ? '' : "/";

		return $dir;
	}


	/**
	 * @param $name
	 * @return string
	 */
	public function cleanFileName($name) {
		$name = Nette\Utils\Strings::replace($name, "~^[.]{2}~", "__");

		return Nette\Utils\Strings::webalize($name, '._', FALSE);
	}
	/**
	 * @param      $dir
	 * @param      $imageName
	 * @param      $routineName
	 * @param bool $ignoreSuffix
	 * @param bool $deleteOld [TRUE|NULL|FALSE]
	 * @return \stdClass
	 * @throws ImageException
	 * @throws Nette\Utils\UnknownImageFileException
	 */
	public function convert($dir, $imageName, $routineName, $ignoreSuffix = TRUE, $deleteOld = TRUE) {
		$routine = $this->getRoutine($routineName);

		$dir = $this->unifiDir($dir);

		if (!is_dir($dir)) {
			throw new ImageException("Directory '$dir' not exist.", ImageException::DIRECTORY_NOT_FOUND);
		}

		if ($ignoreSuffix && !is_file($dir . $imageName)) {
			$nameArr = explode(".", $imageName);

			if (count($nameArr) > 1) {
				unset($nameArr[count($nameArr) - 1]);
				foreach (Nette\Utils\Finder::findFiles(implode(".", $nameArr) . ".*")->in($dir) as $k => $v) {
					$imageName = Nette\Utils\Strings::replace($k, "~^" . $dir . "~", "");

					break;
				}
			}
		}

		if (!is_file($dir . $imageName)) {
			throw new ImageException("Image '$dir$imageName' not found.", ImageException::FILE_NOT_FOUND);
		}

		$image = Nette\Utils\Image::fromFile($dir . $imageName);
		$out = new \stdClass;
		$out->original = (object)[
			'name'   => $imageName,
			'width'  => $image->width,
			'height' => $image->height,
		];

		foreach ($routine as $k => $v) {
			$newDir = $v["dir"];

			if (!is_dir($newDir)) {
				@mkdir($newDir, 0774);
			}

			if (file_exists($newDir . $imageName)) {
				if ($deleteOld === FALSE) {
					continue;
				}
				else if (is_null($deleteOld)) {
					throw new ImageException("", ImageException::FILE_EXIST);
				}
			}

			$flag = [];
			foreach ($v["flags"] as $v2) {
				if (defined('Nette\Utils\Image::' . $v2)) {
					$flag |= constant('Nette\Utils\Image::' . $v2);
				}
			}

			$image->resize($v["width"], $v["height"], $flag);
			$image->sharpen();

			if ($deleteOld && file_exists($newDir . $imageName)) {
				unlink($newDir . $imageName);
			}
			$image->save($newDir . $imageName);

			$out->$k = (object)[
				'name'   => $imageName,
				'width'  => $image->width,
				'height' => $image->height,
			];
		}

		return $out;
	}

	public function clean($imageName, $routineName, $cleanPath = FALSE) {
		$routine = $this->getRoutine($routineName);

		foreach ($routine as $k => $v) {
			$dir = $v["dir"];

			if (file_exists($dir . $imageName)) {
				unlink($dir . $imageName);
			}
		}

		if ($cleanPath) {
			$this->cleanPath($imageName, $routineName);
		}
	}
	public function cleanPath($imageName, $pathName) {
		$path = $this->getPath($pathName);

		$dir = $this->unifiDir($path);

		if (file_exists($dir . $imageName)) {
			unlink($dir . $imageName);
		}
	}
}

class ImageException extends \Exception
{
	const
		PATH_NOT_FOUND = 0,
		ROUTINE_NOT_FOUND = 1,
		DIRECTORY_NOT_FOUND = 2,
		FILE_NOT_FOUND = 3,
		FILE_EXIST = 4;
}