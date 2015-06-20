<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 20:17
 */

namespace Trejjam\Utils\Contents;


use Nette,
	Tracy,
	Trejjam;

class Contents
{
	/**
	 * @var  string
	 */
	protected $configurationDirectory;
	/**
	 * @var string
	 */
	protected $logDirectory;
	/**
	 * @var Tracy\ILogger
	 */
	protected $logger;

	public function __construct($configurationDirectory, $logDirectory = NULL, Tracy\ILogger $logger = NULL)
	{
		$this->configurationDirectory = $configurationDirectory;
		$this->logDirectory = $logDirectory;
		$this->logger = $logger;
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

		$out = Factory::getItemObject(['type' => 'container', 'child' => $configuration], $data);

		if (!is_null($this->logDirectory) && !is_null($this->logger)) {
			@mkdir($this->logger->directory . '/' . $this->logDirectory, 0770);
			chmod($this->logger->directory . '/' . $this->logDirectory . '/', 0770);
			$this->logger->log(var_export($out->getRemovedItems(), TRUE), $this->logDirectory . '/' . $name);
		}

		return $out;
	}
}
