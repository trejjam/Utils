<?php

namespace Trejjam\Utils\DI;

use Trejjam;

class LatteExtension extends Trejjam\BaseExtension\DI\BaseExtension
{
	protected $classesDefinition = [
		'filter.json' => Trejjam\Utils\Latte\Filter\Json::class,
	];

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$latteFactory = $builder->getDefinition('latte.latteFactory');
		$latteFactory->addSetup('addFilter', ['json', [$this->prefix('@filter.json'), 'filter']]);
	}
}
