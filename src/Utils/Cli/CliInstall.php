<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 6.12.14
 * Time: 2:25
 */

namespace Trejjam\Utils;

use Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Output\OutputInterface,
	Nette;

class CliInstall extends Command
{
	const
		FILE_LABELS_TABLE = "utils__labels",
		FILE_PAGE_INFO_TABLE = "page_info";

	/**
	 * @var \Nette\Database\Context @inject
	 */
	public $database;

	protected function configure() {
		$this->setName('Utils:install')
			 ->setDescription('Install default tables');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->database->query($this->getFile(self::FILE_LABELS_TABLE));
		$this->database->query($this->getFile(self::FILE_PAGE_INFO_TABLE));
	}
	protected function getFile($file) {
		return file_get_contents(__DIR__."/../../../sql/". $file.".sql");
	}
}