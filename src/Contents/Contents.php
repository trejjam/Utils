<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 20:17
 */

namespace Trejjam\Utils\Contents;


use Nette,
	Trejjam;

class Contents
{
	/**
	 * @var  string
	 */
	protected $configurationDirectory;

	public function __construct($configurationDirectory)
	{
		$this->configurationDirectory = $configurationDirectory;
	}

	protected function getFilePath($name)
	{
		return $this->configurationDirectory . '/' . $name . '.neon';
	}
	/**
	 * @param $name
	 * @return bool
	 */
	protected function hasConfiguration($name)
	{
		return is_file($this->getFilePath($name));
	}

	/**
	 * @param $name
	 * @return array
	 */
	protected function loadConfiguration($name)
	{
		if (!$this->hasConfiguration($name)) {
			throw new Trejjam\Utils\InvalidArgumentException("Configuration with name '$name' not exist.", Trejjam\Utils\Exception::CONTENTS_MISSING_CONFIGURATION);
		}

		return Nette\Neon\Neon::decode(file_get_contents($this->getFilePath($name)));
	}

	public function getDataObject($name, $data)
	{
		$configuration = $this->loadConfiguration($name);

		if (!is_array($data) && !is_object($data)) {
			try {
				$data = Nette\Utils\Json::decode($data);
			}
			catch (Nette\Utils\JsonException $e) {
				if ($data == '' || is_null($data)) {
					$data = NULL;
				}
				else {
					throw new Trejjam\Utils\LogicException('Json::decode problem on non empty data object', Trejjam\Utils\Exception::CONTENTS_JSON_DECODE, $e);
				}
			}
		}

		return Factory::getItemObject(['type' => 'container', 'child' => $configuration], $data);
	}
}
