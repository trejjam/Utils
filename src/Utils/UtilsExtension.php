<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 26. 10. 2014
 * Time: 17:38
 */

namespace Trejjam\DI;

use Nette;

class UtilsExtension extends Nette\DI\CompilerExtension
{
	private $defaults = [
		'labels'   => [
			'table'     => 'utils__labels',
			'id'        => 'id',
			'namespace' => [
				'name'    => 'namespace',
				'default' => 'default'
			],
			'name'      => 'name',
			'value'     => 'value',
		],
		'cache'    => [
			//not implemented yet
			"use"     => FALSE,
			"name"    => "utils",
			"timeout" => "10 minutes"
		],
		'debugger' => FALSE, //not implemented yet
	];

	public function loadConfiguration() {
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$labels = $builder->addDefinition($this->prefix('labels'))
						  ->setClass('Trejjam\Utils\Labels')
						  ->addSetup("setConfig", [
							  "config" => $config["labels"],
						  ]);

		$label = $builder->addDefinition($this->prefix('label'))
						  ->setClass('Trejjam\Utils\Components\Label')
						  ->addSetup("setup");


		if (class_exists('\Symfony\Component\Console\Command\Command')) {
			$command = [
				"cliLabels"  => "CliLabels",
				"cliInstall" => "CliInstall",
			];

			foreach ($command as $k => $v) {
				$builder->addDefinition($this->prefix($k))
						->setClass('Trejjam\Utils\\' . $v)
						->addTag("kdyby.console.command");
			}
		}

		/*
		if ($config["cache"]["use"]) {
			$builder->addDefinition($this->prefix("cache"))
					->setFactory('Nette\Caching\Cache')
					->setArguments(['@cacheStorage', $config["cache"]["name"]])
					->setAutowired(FALSE);

			$accessControlList->setArguments([$this->prefix("@cache")])
							  ->addSetup("setCacheParams", ["cacheParams" => [
								  Nette\Caching\Cache::EXPIRE => $config["cache"]["timeout"]
							  ]]);
		}


		if ($config["debugger"]) {
			$builder->addDefinition($this->prefix("panel"))
					->setClass('Trejjam\Utils')
					->setAutowired(FALSE);

			$accessControlList->addSetup('injectPanel', array($this->prefix("@panel")));
		}*/
	}
}