<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 6.12.14
 * Time: 1:45
 */

namespace Trejjam\Utils\Cli;

use Symfony\Component\Console\Command\Command,
	Trejjam;

abstract class Helper extends Command
{
	/**
	 * @var Trejjam\Utils\Labels\Labels
	 */
	protected $labels;

	public function __construct(Trejjam\Utils\Labels\Labels $labels)
	{
		parent::__construct();

		$this->labels = $labels;
	}
}
