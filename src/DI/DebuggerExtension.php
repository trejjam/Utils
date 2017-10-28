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
		'sslAuthorizedDn' => NULL,
		'logIgnoreEmail'  => [],
		'siteMode'        => NULL,
		'email'           => '@Nette\Mail\IMailer',
		'blobService'     => NULL,
		'blobPrefix'      => '',
	];

	public function loadConfiguration() : void
	{
		if (array_key_exists('sslAuthorizedDn', $this->getContainerBuilder()->parameters)) {
			$this->default['sslAuthorizedDn'] = $this->getContainerBuilder()->parameters['sslAuthorizedDn'];
		}
		if (array_key_exists('siteMode', $this->getContainerBuilder()->parameters)) {
			$this->default['siteMode'] = $this->getContainerBuilder()->parameters['siteMode'];
		}

		$this->validateConfig($this->default);

		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();

		$tracyLogger = $builder->getDefinition('tracy.logger');
		$tracyLogger->setFactory('Trejjam\Utils\Debugger\Debugger::getLogger')
					->addSetup('setEmailClass', [$this->config['email']])
					->addSetup('setEmailSnooze', [$this->config['snoze']])
					->addSetup('setHost', [$this->config['host']])
					->addSetup('setPath', [$this->config['path']]);

		$blueScreen = $builder->getDefinition('tracy.blueScreen');
		$blueScreen->setFactory('Trejjam\Utils\Debugger\Debugger::getBlueScreen')
				   ->addSetup('setSslAuthorizedDn', [$this->config['sslAuthorizedDn'], $this->config['logIgnoreEmail']])
				   ->addSetup('setSiteMode', [$this->config['siteMode']]);

		if ( !is_null($this->config['blobService'])) {
			$builder->addDefinition($this->prefix('storage'))
					->setClass(Trejjam\Utils\Debugger\Storage\Storage::class)
					->setArguments(
						[
							$this->config['blobService'],
							$this->config['blobPrefix'],
						]
					)
					->setAutowired(FALSE);

			$blueScreen->addSetup('setLogStorage', [$this->prefix('@storage')]);
		}
	}
}
