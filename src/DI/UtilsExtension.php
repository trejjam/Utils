<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 26. 10. 2014
 * Time: 17:38
 */

namespace Trejjam\Utils\DI;

use Nette;
use Trejjam\Utils\Contents\Contents;

class UtilsExtension extends Nette\DI\CompilerExtension
{
	protected $defaults = [
		'flashes'  => [
			'enable' => FALSE,
		],
		'browser'  => [
			'enable' => FALSE,
		],
		'labels'   => [
			'enable'        => FALSE,
			'componentName' => 'labels',
			'table'         => 'utils__labels',
			'keys'          => [
				'id'        => 'id',
				'namespace' => [
					'name'    => 'namespace',
					'default' => 'default'
				],
				'name'      => 'name',
				'value'     => 'value',
			],
		],
	];

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		Nette\Utils\Validators::assert($config, 'array');

		$layout = $builder->addDefinition($this->prefix('baseLayout'))
						  ->setClass('Trejjam\Utils\Layout\BaseLayout')
						  ->addSetup("setConfigurations", [
							  "configurations" => $config,
						  ]);

		if ($config['labels']['enable']) {
			$labels = $builder->addDefinition($this->prefix('labels'))
							  ->setClass('Trejjam\Utils\Labels\Labels')
							  ->addSetup("setConfigurations", [
								  "configurations" => $config["labels"],
							  ]);

			$layout->setArguments([$this->prefix('labels')]);
		}

		if ($config['browser']['enable']) {
			$browser = $builder->addDefinition($this->prefix('browser'))
							   ->setClass('Browser\Browser');
		}

		if (class_exists('\Symfony\Component\Console\Command\Command')) {
			$command = [
				"cli.install" => "Install",
			];
			if ($config['labels']['enable']) {
				$command["cli.labels"] = "Labels";
			}

			foreach ($command as $k => $v) {
				$builder->addDefinition($this->prefix($k))
						->setClass('Trejjam\Utils\Cli\\' . $v)
						->addTag("kdyby.console.command");
			}
		}
	}
}
