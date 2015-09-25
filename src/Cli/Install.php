<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 6.12.14
 * Time: 2:25
 */

namespace Trejjam\Utils\Cli;

use Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Output\OutputInterface,
	Nette;

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
