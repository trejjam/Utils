<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 20:17
 */

namespace Trejjam\Utils\Contents;


use Nette,
	Nette\Application\UI,
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

	/**
	 * @var Trejjam\Utils\Contents\Items\SubType[]
	 */
	protected $subTypes = [];

	public function __construct($configurationDirectory, $logDirectory = NULL, Tracy\ILogger $logger = NULL)
	{
		$this->configurationDirectory = $configurationDirectory;
		$this->logDirectory = $logDirectory;
		$this->logger = $logger;
	}

	public function addSubType(Trejjam\Utils\Contents\Items\SubType $subType)
	{
		$this->subTypes[$subType->getName()] = $subType;
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
				$data = Nette\Utils\Json::decode($data, Nette\Utils\Json::FORCE_ARRAY);
			}
			catch (Nette\Utils\JsonException $e) {
				throw new Trejjam\Utils\LogicException('Json::decode problem', Trejjam\Utils\Exception::CONTENTS_JSON_DECODE, $e);
			}
		}

		$out = Factory::getItemObject(['type' => 'container', 'child' => $configuration], $data, $this->subTypes);

		if (!is_null($this->logDirectory) && !is_null($this->logger)) {
			@mkdir($this->logger->directory . '/' . $this->logDirectory, 0770);
			chmod($this->logger->directory . '/' . $this->logDirectory . '/', 0770);
			$this->logger->log(var_export($out->getRemovedItems(), TRUE), $this->logDirectory . '/' . $name);
		}

		return $out;
	}

	/**
	 * @param Items\Base  $itemContainer
	 * @param array       $userOptions
	 * @param null|string $contentName
	 * @return UI\Form
	 */
	public function createForm(Trejjam\Utils\Contents\Items\Base $itemContainer, $userOptions = [], $contentName = NULL)
	{
		$form = new UI\Form;

		$ids = [];
		$itemContainer->generateForm($itemContainer, $form, 'root', '', $ids, $userOptions);

		$form->onSuccess[] = function (UI\Form $form) use ($itemContainer, $contentName) {
			return $this->proceedEditForm($form, $itemContainer, $contentName);
		};
		$form->addProtection();

		return $form;
	}

	public function proceedEditForm(UI\Form $form, Trejjam\Utils\Contents\Items\Base $itemContainer, $contentName)
	{
		$values = $form->getValues();

		$itemContainer->update($values->root);

		if (!is_null($this->logDirectory) && !is_null($this->logger)) {
			@mkdir($this->logger->directory . '/' . $this->logDirectory, 0770);
			chmod($this->logger->directory . '/' . $this->logDirectory . '/', 0770);
			$this->logger->log(var_export($itemContainer->getUpdated(), TRUE), $this->logDirectory . '/' . (is_null($contentName) ? '__updated__' : $contentName));
		}

		return TRUE;
	}
}
