<?php

namespace Trejjam\Utils\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette;

class Install extends Command
{
	const
		FILE_LABELS_TABLE = "utils__labels";

	/**
	 * @var \Nette\Database\Context @inject
	 */
	public $database;

	protected function configure()
	{
		$this->setName('Utils:install')
			 ->setDescription('Install default tables');
	}
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$connection = $this->database->getConnection();
		$driverName = $connection->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
		Nette\Database\Helpers::loadFromFile($connection, $this->getFileName($driverName . '.' . static::FILE_LABELS_TABLE));
	}
	protected function getFileName($file)
	{
		return __DIR__ . "/../../sql/" . $file . ".sql";
	}
}
