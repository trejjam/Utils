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
	const TAG_CONTENTS_SUBTYPES = 'trejjam.utils.contents';

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
		'contents' => [
			'enable'                 => FALSE,
			'configurationDirectory' => '%appDir%/config/contents',
			'logDirectory'           => NULL,
			'subTypes'               => [],
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

		if ($config['contents']['enable']) {
			$contentsArguments = [
				$config['contents']['configurationDirectory'],
				$config['contents']['logDirectory'],
			];
			if (!is_null($config['contents']['logDirectory'])) {
				$contentsArguments[2] = '@tracy.logger';
			}

			$contents = $builder->addDefinition($this->prefix('contents'))
								->setClass('Trejjam\Utils\Contents\Contents')
								->setArguments($contentsArguments);

			foreach ($config['contents']['subTypes'] as $subTypeName => $subType) {
				$def = $builder->addDefinition($this->prefix('contents.' . md5(Nette\Utils\Json::encode($subType))));
				$def->addSetup('setName', [$subTypeName]);
				list($def->factory) = Nette\DI\Compiler::filterArguments([
					is_string($subType) ? new Nette\DI\Statement($subType) : $subType
				]);
				$def->setAutowired(FALSE);
				$def->setInject(FALSE);
				$def->addTag(self::TAG_CONTENTS_SUBTYPES);
			}
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

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if ($config['contents']['enable']) {
			$contents = $builder->getDefinition($this->prefix('contents'));
			foreach (array_keys($builder->findByTag(self::TAG_CONTENTS_SUBTYPES)) as $serviceName) {
				$contents->addSetup('addSubType', ['@' . $serviceName]);
			}
		}
	}
}
