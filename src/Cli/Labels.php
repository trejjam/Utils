<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 6.12.14
 * Time: 2:25
 */

namespace Trejjam\Utils\Cli;

use Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Output\OutputInterface,
	Nette;

class Labels extends Helper
{
	protected function configure()
	{
		$this->setName('Utils:labels')
			 ->setDescription('Edit labels')
			 ->addArgument(
				 'label',
				 InputArgument::OPTIONAL,
				 'Enter label name'
			 )->addArgument(
				'value',
				InputArgument::OPTIONAL,
				'Enter label value'
			)->addOption(
				'namespace',
				's',
				InputOption::VALUE_REQUIRED,
				'Set namespace'
			)->addOption(
				'delete',
				'd',
				InputOption::VALUE_NONE,
				'Delete label'
			);
	}
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$namespace = $input->getOption('namespace');
		$delete = $input->getOption('delete');

		$label = $input->getArgument('label');
		$value = $input->getArgument('value');

		if ($delete) {
			$this->labels->delete($label, $namespace);
		}
		else {
			$this->labels->setData($label, $value, $namespace);
		}
	}
}
