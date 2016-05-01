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

class DebuggerExtension extends Nette\DI\CompilerExtension
{
	protected $default = [
		'snoze'           => '1 day',
		'host'            => NULL, //NULL mean auto
		'path'            => '/log/',
		'sslAuthorizedDn' => '%sslAuthorizedDn%',
		'logIgnoreEmail'  => [],
		'siteMode'        => '%siteMode%',
	];

	protected function createConfig()
	{
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
					->addSetup('setEmailClass', ['@Nette\Mail\IMailer'])
					->addSetup('setEmailSnooze', [$config['snoze']])
					->addSetup('setHost', [$config['host']])
					->addSetup('setPath', [$config['path']]);

		$tracyLogger = $builder->getDefinition('tracy.blueScreen');
		$tracyLogger->setFactory('Trejjam\Utils\Debugger\Debugger::getBlueScreen')
					->addSetup('setSslAuthorizedDn', [$config['sslAuthorizedDn'], $config['logIgnoreEmail']])
					->addSetup('setSiteMode', [$config['siteMode']]);
	}

}
