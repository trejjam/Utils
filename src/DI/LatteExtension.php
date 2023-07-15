<?php
declare(strict_types=1);

namespace Trejjam\Utils\DI;

use Nette\DI\CompilerExtension;
use Trejjam;
use Nette\DI\Definitions\FactoryDefinition;

final class LatteExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('filter.json'))
            ->setType(Trejjam\Utils\Latte\Filter\Json::class);
        $builder->addDefinition($this->prefix('filter.md5'))
            ->setType(Trejjam\Utils\Latte\Filter\Md5::class);
        $builder->addDefinition($this->prefix('filter.sha1'))
            ->setType(Trejjam\Utils\Latte\Filter\Sha1::class);

        /** @var FactoryDefinition $latteFactoryDefinition */
        $latteFactoryDefinition = $builder->getDefinition('latte.latteFactory');
        $latteFactoryDefinition->getResultDefinition()
            ->addSetup('addFilter', ['json', [$this->prefix('@filter.json'), 'filter']])
            ->addSetup('addFilter', ['md5', [$this->prefix('@filter.md5'), 'filter']])
            ->addSetup('addFilter', ['sha1', [$this->prefix('@filter.sha1'), 'filter']]);
    }
}
