<?php
declare(strict_types=1);

namespace Trejjam\Utils\DI;

use Nette;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Trejjam;

final class UtilsExtension extends Nette\DI\CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'components' => Expect::structure([
                'paging' => Expect::structure([
                    'template' => Expect::string()->default(__DIR__ . '/../templates/paging.latte'),
                ]),
                'listing' => Expect::structure([
                    'template' => Expect::string()->default(__DIR__ . '/../templates/list.latte'),
                ]),
                'filter' => Expect::structure([
                    'template' => Expect::string()->default(__DIR__ . '/../templates/sortLink.latte'),
                ]),
            ]),
        ]);
    }

    public function beforeCompile()
    {
        parent::beforeCompile();

        $builder = $this->getContainerBuilder();
        $config = $this->createConfig();

        $builder->addDefinition($this->prefix('components.listingFactory'))
            ->setFactory(Trejjam\Utils\Components\IListingFactory::class)
            ->setArguments([
                'templateFile' => $config['components']['listing']['template'],
                'filterFactory' => $this->prefix('@components.filterFactory'),
            ]);

        $builder->addDefinition($this->prefix('components.filterFactory'))
            ->setFactory(Trejjam\Utils\Components\IFilterFactory::class)
            ->setArguments([
                'templateFile' => $config['components']['filter']['template']
            ]);

        $builder->addDefinition($this->prefix('components.pagingFactory'))
            ->setFactory(Trejjam\Utils\Components\IPagingFactory::class)
            ->setArguments([
                'templateFile' =>$config['components']['paging']['template']
            ]);
    }
}
