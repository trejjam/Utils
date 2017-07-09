<?php

namespace Trejjam\Utils\DI;

use Trejjam;

class LatteExtension extends Trejjam\BaseExtension\DI\BaseExtension
{
	protected $classesDefinition = [
		'filter.json' => Trejjam\Utils\Latte\Filter\Json::class,
		'filter.md5'  => Trejjam\Utils\Latte\Filter\Md5::class,
		'filter.sha1' => Trejjam\Utils\Latte\Filter\Sha1::class,
	];

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$latteFactory = $builder->getDefinition('latte.latteFactory');
		$latteFactory->addSetup('addFilter', ['json', [$this->prefix('@filter.json'), 'filter']]);
		$latteFactory->addSetup('addFilter', ['md5', [$this->prefix('@filter.md5'), 'filter']]);
		$latteFactory->addSetup('addFilter', ['sha1', [$this->prefix('@filter.sha1'), 'filter']]);
	}
}
