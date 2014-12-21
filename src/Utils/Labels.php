<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 3.5.14
 * Time: 19:06
 */

namespace Trejjam\Utils;

use Nette;

class Labels
{
	/**
	 * @var Nette\Database\Context
	 */
	protected $database;

	protected $config;

	protected $init = FALSE;
	protected $namespace = [];
	protected $notExistLabels = [];

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	public function setConfig(array $config) {
		$this->config = $config;
	}

	protected function init() {
		foreach ($this->database->table($this->config["table"]) as $v) {
			if (!isset($this->namespace[$v->{$this->config["namespace"]["name"]}])) {
				$this->namespace[$v->{$this->config["namespace"]["name"]}] = [];
			}
			$this->namespace[$v->{$this->config["namespace"]["name"]}][$v->{$this->config["name"]}] = $v->{$this->config["value"]};
		}
		$this->init = TRUE;
	}
	protected function isInit() {
		if (!$this->init) {
			$this->init();
		}
	}
	public function reInit() {
		$this->init = FALSE;
		$this->namespace = [];
	}

	public function getNamespaces() {
		$this->isInit();

		return array_keys($this->namespace);
	}
	public function getKeys($namespace) {
		$this->isInit();

		if (!isset($this->namespace[$namespace])) {
			throw new \Exception("Namespace not exist");
		}
		return array_keys($this->namespace[$namespace]);
	}

	protected function getRawData($name, $namespace = NULL) {
		$this->isInit();

		if (isset($this->namespace[$namespace])) {
			if (isset($this->namespace[$namespace][$name])) {
				return $this->namespace[$namespace][$name];
			}
		}

		throw new \Exception("Name not found in label namespace");
	}
	public function getData($name, $namespace = NULL, $useFallBack = TRUE) {
		if (is_null($namespace)) {
			$namespace = $this->config["namespace"]["default"];
		}

		try {
			return $this->getRawData($name, $namespace);
		}
		catch (\Exception $e) {
			if ($useFallBack && $namespace != $this->config["namespace"]["default"]) {
				return $this->getData($name);
			}

			$notExistLabels[$name] = TRUE;

			return "";
		}
	}
	public function setData($name, $value, $namespace = NULL) {
		if (is_null($value)) {
			$this->delete($name, $namespace);
		}
		else {
			if (is_null($namespace)) {
				$namespace = $this->config["namespace"]["default"];
			}

			try {
				$this->getRawData($name, $namespace);

				$this->database->table($this->config["table"])->where([
					$this->config["namespace"]["name"] => $namespace,
					$this->config["name"]              => $name,
				])->update([
					$this->config["value"] => $value,
				]);
			}
			catch (\Exception $e) {
				$this->database->table($this->config["table"])->insert([
					$this->config["namespace"]["name"] => $namespace,
					$this->config["name"]              => $name,
					$this->config["value"]             => $value,
				]);
			}
		}

		$this->reInit();
	}
	public function delete($name, $namespace = NULL) {
		if (is_null($namespace)) {
			$namespace = $this->config["namespace"]["default"];
		}

		try {
			$this->getRawData($name, $namespace);

			$this->database->table($this->config["table"])->where([
				$this->config["namespace"]["name"] => $namespace,
				$this->config["name"]              => $name,
			])->delete();

			$this->reInit();
		}
		catch (\Exception $e) {
			throw new \Exception("Label with name $name and $namespace namespace not exist.");
		}
	}

	public function &__get($name) {
		$labelsData= new LabelsData($name, $this);
		return $labelsData;
	}
	public function __set($name, $data) {
		$this->setData($name, $data);

		return $data;
	}
}

class LabelsData
{
	protected
		$name,
		$labels;

	public function __construct($name, Labels $labels) {
		$this->name = $name;
		$this->labels = $labels;
	}

	public function __toString() {
		return $this->labels->getData($this->name);
	}
	public function &__get($name) {
		$labelData= $this->labels->getData($name, $this->name);
		return $labelData;
	}
	public function __set($name, $data) {
		$this->labels->setData($name, $data, $this->name);

		return $data;
	}
}