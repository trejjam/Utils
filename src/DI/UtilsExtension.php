<?php

namespace Trejjam\Utils\DI;

use Nette;
use Trejjam;

class UtilsExtension extends Nette\DI\CompilerExtension
{
	protected $classesDefinition = [
		'sinergi.browser'   => 'Sinergi\BrowserDetector\Browser',
		'sinergi.os'        => 'Sinergi\BrowserDetector\Os',
		'sinergi.device'    => 'Sinergi\BrowserDetector\Device',
		'sinergi.language'  => 'Sinergi\BrowserDetector\Language',
	];

	protected $factoriesDefinition = [
		'components.listingFactory' => 'Trejjam\Utils\Components\IListingFactory',
		'components.filterFactory'  => 'Trejjam\Utils\Components\IFilterFactory',
		'components.pagingFactory'  => 'Trejjam\Utils\Components\IPagingFactory',
	];

	protected function createConfig()
	{
		$config = $this->getConfig(
			[
				'sinergi'    => [
					'enable' => FALSE,
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
			]
		);

		Nette\Utils\Validators::assert($config, 'array');

		return $config;
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->createConfig();

		foreach ($this->classesDefinition as $k => $v) {
			list($firstKey) = explode('.', $k);
			if ( !isset($config[$firstKey]) || !isset($config[$firstKey]['enable']) || $config[$firstKey]['enable']) {
				$classes[$k] = $builder->addDefinition($this->prefix($k))
									   ->setClass($v);
			}
		}

		/** @var Nette\DI\ServiceDefinition[] $factories */
		$factories = [];
		foreach ($this->factoriesDefinition as $k => $v) {
			$factories[$k] = $builder->addDefinition($this->prefix($k))
									 ->setImplement($v);
		}

		$factories['components.listingFactory']->setArguments(
			[
				'templateFile'  => $config['components']['listing']['template'],
				'filterFactory' => $this->prefix('@components.filterFactory'),
			]
		);
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();
		$config = $this->createConfig();

		/** @var Nette\DI\ServiceDefinition[] $classes */
		$classes = [];

		foreach ($this->classesDefinition as $k => $v) {
			list($firstKey) = explode('.', $k);
			if ( !isset($config[$firstKey]) || !isset($config[$firstKey]['enable']) || $config[$firstKey]['enable']) {
				$classes[$k] = $builder->getDefinition($this->prefix($k));
			}
		}

		/** @var Nette\DI\ServiceDefinition[] $factories */
		$factories = [];
		foreach ($this->factoriesDefinition as $k => $v) {
			$factories[$k] = $builder->getDefinition($this->prefix($k));
		}

		$factories['components.filterFactory']->setArguments([$config['components']['filter']['template']]);
		$factories['components.pagingFactory']->setArguments([$config['components']['paging']['template']]);
	}
}
