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

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		Nette\Utils\Validators::assert($config, 'array');

		$classesDefinition = [
			'baseLayout'         => 'Trejjam\Utils\Layout\BaseLayout',
			'labels'             => 'Trejjam\Utils\Labels\Labels',
			'browser'            => 'Browser\Browser',
			'components.listing' => 'Trejjam\Utils\Components\ListingFactory',
			'components.filter'  => 'Trejjam\Utils\Components\FilterFactory',
			'components.paging'  => 'Trejjam\Utils\Components\PagingFactory',
		];
		$factoriesDefinition = [
			'components.listingFactory' => 'Trejjam\Utils\Components\IListingFactory',
			'components.filterFactory'  => 'Trejjam\Utils\Components\IFilterFactory',
			'components.pagingFactory'  => 'Trejjam\Utils\Components\IPagingFactory',
		];

		/** @var Nette\DI\ServiceDefinition[] $classes */
		$classes = [];

		foreach ($classesDefinition as $k => $v) {
			if (!isset($config[$k]) || !isset($config[$k]['enable']) || $config[$k]['enable']) {
				$classes[$k] = $builder->addDefinition($this->prefix($k))
									   ->setClass($v);
			}
		}

		/** @var Nette\DI\ServiceDefinition[] $factories */
		$factories = [];

		foreach ($factoriesDefinition as $k => $v) {
			$factories[$k] = $builder->addDefinition($this->prefix($k))
									 ->setImplement($v);
		}

		$classes['baseLayout']->addSetup('setConfigurations', [
			'configurations' => $config,
		]);

		if ($config['labels']['enable']) {
			$classes['labels']->addSetup('setConfigurations', [
				'configurations' => $config['labels'],
			]);

			$classes['baseLayout']->setArguments([$this->prefix('@labels')]);
		}

		$classes['components.listing']->setArguments([$config['components']['listing']['template']]);
		$classes['components.filter']->setArguments([$config['components']['filter']['template']]);
		$classes['components.paging']->setArguments([$config['components']['paging']['template']]);

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
