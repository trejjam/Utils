<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 26. 10. 2014
 * Time: 17:38
 */

namespace Trejjam\Utils\DI;

use Nette,
	Trejjam;

class UtilsExtension extends Nette\DI\CompilerExtension
{
	protected $defaults = [
		'flashes'    => [
			'enable' => FALSE,
		],
		'browser'    => [
			'enable' => FALSE,
		],
		'labels'     => [
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
		'components' => [
			'paging'  => [
				'template' => __DIR__ . '/../templates/paging.latte',
			],
			'listing' => [
				'template' => __DIR__ . '/../templates/list.latte',
			],
			'filter'  => [
				'template' => __DIR__ . '/../templates/sortLink.latte',
			],
		],
	];

	protected $classesDefinition = [
		'layout.baseLayout' => 'Trejjam\Utils\Layout\BaseLayout',
		'labels.labels'     => 'Trejjam\Utils\Labels\Labels',
		'browser'           => 'Browser\Browser',
		//'components.listing' => 'Trejjam\Utils\Components\ListingFactory',
		//'components.filter'  => 'Trejjam\Utils\Components\FilterFactory',
		//'components.paging'  => 'Trejjam\Utils\Components\PagingFactory',
	];

	protected $factoriesDefinition = [
		'components.listingFactory' => 'Trejjam\Utils\Components\IListingFactory',
		'components.filterFactory'  => 'Trejjam\Utils\Components\IFilterFactory',
		'components.pagingFactory'  => 'Trejjam\Utils\Components\IPagingFactory',
	];

	protected function getConfiguration()
	{
		$config = $this->getConfig($this->defaults);

		Nette\Utils\Validators::assert($config, 'array');

		return $config;
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfiguration();

		foreach ($this->classesDefinition as $k => $v) {
			if (!isset($config[$k]) || !isset($config[$k]['enable']) || $config[$k]['enable']) {
				$classes[$k] = $builder->addDefinition($this->prefix($k))
									   ->setClass($v);
			}
		}

		foreach ($this->factoriesDefinition as $k => $v) {
			$factories[$k] = $builder->addDefinition($this->prefix($k))
									 ->setImplement($v);
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
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfiguration();

		/** @var Nette\DI\ServiceDefinition[] $classes */
		$classes = [];

		foreach ($this->classesDefinition as $k => $v) {
			if (!isset($config[$k]) || !isset($config[$k]['enable']) || $config[$k]['enable']) {
				$classes[$k] = $builder->getDefinition($this->prefix($k));
			}
		}

		/** @var Nette\DI\ServiceDefinition[] $factories */
		$factories = [];
		foreach ($this->factoriesDefinition as $k => $v) {
			$factories[$k] = $builder->getDefinition($this->prefix($k));
		}

		$classes['layout.baseLayout']->addSetup('setConfigurations', [
			'configurations' => $config,
		]);

		if ($config['labels']['enable']) {
			$classes['labels.labels']->addSetup('setConfigurations', [
				'configurations' => $config['labels'],
			]);

			$classes['layout.baseLayout']->setArguments([$this->prefix('@labels.labels')]);
		}

		$factories['components.listingFactory']->setArguments([$config['components']['listing']['template']]);
		$factories['components.filterFactory']->setArguments([$config['components']['filter']['template']]);
		$factories['components.pagingFactory']->setArguments([$config['components']['paging']['template']]);
	}
}
