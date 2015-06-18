<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 3.5.14
 * Time: 19:06
 */

namespace Trejjam\Utils\Labels;

use Nette,
	Trejjam;

class Labels
{
	/**
	 * @var Nette\Database\Context
	 */
	protected $database;
	/**
	 * @var Component
	 */
	protected $labelComponent;

	protected $configurations;

	protected $init           = FALSE;
	protected $namespace      = [];
	protected $notExistLabels = [];

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
		$this->labelComponent = new Component;
		$this->labelComponent->setup($this);
	}
	public function setConfigurations(array $configurations)
	{
		$this->configurations = $configurations;
	}
	public function create()
	{
		return $this->labelComponent;
	}

	protected function init()
	{
		foreach ($this->database->table($this->configurations["table"]) as $v) {
			if (!isset($this->namespace[$v->{$this->configurations['keys']["namespace"]["name"]}])) {
				$this->namespace[$v->{$this->configurations['keys']["namespace"]["name"]}] = [];
			}
			$this->namespace[$v->{$this->configurations['keys']["namespace"]["name"]}][$v->{$this->configurations['keys']["name"]}] = $v->{$this->configurations['keys']["value"]};
		}
		$this->init = TRUE;
	}
	protected function isInit()
	{
		if (!$this->init) {
			$this->init();
		}
	}
	public function reInit()
	{
		$this->init = FALSE;
		$this->namespace = [];
	}

	public function getNamespaces()
	{
		$this->isInit();

		return array_keys($this->namespace);
	}
	public function getKeys($namespace)
	{
		$this->isInit();

		if (!isset($this->namespace[$namespace])) {
			throw new Trejjam\Utils\InvalidArgumentException("Namespace '$namespace' not exist");
		}

		return array_keys($this->namespace[$namespace]);
	}

	protected function getRawData($name, $namespace = NULL)
	{
		$this->isInit();

		if (isset($this->namespace[$namespace])) {
			if (isset($this->namespace[$namespace][$name])) {
				return $this->namespace[$namespace][$name];
			}
		}

		throw new Trejjam\Utils\InvalidArgumentException("Name '$name' not found in label namespace");
	}
	public function getData($name, $namespace = NULL, $useFallBack = TRUE)
	{
		if (is_null($namespace)) {
			$namespace = $this->configurations['keys']["namespace"]["default"];
		}

		try {
			return $this->getRawData($name, $namespace);
		}
		catch (\Exception $e) {
			if ($useFallBack && $namespace != $this->configurations['keys']["namespace"]["default"]) {
				return $this->getData($name);
			}

			$notExistLabels[$name] = TRUE;

			return "";
		}
	}
	public function setData($name, $value, $namespace = NULL)
	{
		if (is_null($value)) {
			$this->delete($name, $namespace);
		}
		else {
			if (is_null($namespace)) {
				$namespace = $this->configurations['keys']["namespace"]["default"];
			}

			try {
				$this->getRawData($name, $namespace);

				$this->database->table($this->configurations["table"])->where([
					$this->configurations['keys']["namespace"]["name"] => $namespace,
					$this->configurations['keys']["name"]              => $name,
				])->update([
					$this->configurations['keys']["value"] => $value,
				]);
			}
			catch (\Exception $e) {
				$this->database->table($this->configurations["table"])->insert([
					$this->configurations['keys']["namespace"]["name"] => $namespace,
					$this->configurations['keys']["name"]              => $name,
					$this->configurations['keys']["value"]             => $value,
				]);
			}
		}

		$this->reInit();
	}
	public function delete($name, $namespace = NULL)
	{
		if (is_null($namespace)) {
			$namespace = $this->configurations['keys']["namespace"]["default"];
		}

		try {
			$this->getRawData($name, $namespace);

			$this->database->table($this->configurations["table"])->where([
				$this->configurations['keys']["namespace"]["name"] => $namespace,
				$this->configurations['keys']["name"]              => $name,
			])->delete();

			$this->reInit();
		}
		catch (\Exception $e) {
			throw new  Trejjam\Utils\InvalidArgumentException("Label with name $name and $namespace namespace not exist.");
		}
	}

	public function &__get($name)
	{
		$labelsData = new Data($name, $this);

		return $labelsData;
	}
	public function __set($name, $data)
	{
		$this->setData($name, $data);

		return $data;
	}
}
