<?php
declare(strict_types=1);

namespace Trejjam\Utils\DI;

use Nette;
use Trejjam;

class DebuggerExtension extends Nette\DI\CompilerExtension
{
	protected $default = [
		'snoze'           => '1 day',
		'host'            => NULL, //NULL mean auto
		'path'            => '/log/',
		'sslAuthorizedDn' => '%sslAuthorizedDn%',
		'logIgnoreEmail'  => [],
		'siteMode'        => '%siteMode%',
		'email'           => NULL,
		'blobService'     => NULL,
		'blobPrefix'      => '',
	];

	protected function createConfig()
	{
		if ( !array_key_exists('sslAuthorizedDn', $this->getContainerBuilder()->parameters)) {
			$this->getContainerBuilder()->parameters['sslAuthorizedDn'] = NULL;
		}

		$config = $this->getConfig($this->default);

		Nette\Utils\Validators::assert($config, 'array');

		return $config;
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->createConfig();

		$tracyLogger = $builder->getDefinition('tracy.logger');
		$tracyLogger->setFactory('Trejjam\Utils\Debugger\Debugger::getLogger')
					->addSetup('setEmailClass', [$config['email']])
					->addSetup('setEmailSnooze', [$config['snoze']])
					->addSetup('setHost', [$config['host']])
					->addSetup('setPath', [$config['path']]);

		$blueScreen = $builder->getDefinition('tracy.blueScreen');
		$blueScreen->setFactory('Trejjam\Utils\Debugger\Debugger::getBlueScreen')
				   ->addSetup('setSslAuthorizedDn', [$config['sslAuthorizedDn'], $config['logIgnoreEmail']])
				   ->addSetup('setSiteMode', [$config['siteMode']]);

		if ( !is_null($config['blobService'])) {
			$builder->addDefinition($this->prefix('storage'))
					->setClass(Trejjam\Utils\Debugger\Storage\Storage::class)
					->setArguments(
						[
							$config['blobService'],
							$config['blobPrefix'],
						]
					)
					->setAutowired(FALSE);

			$blueScreen->addSetup('setLogStorage', [$this->prefix('@storage')]);
		}
	}
}
