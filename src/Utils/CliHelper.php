<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 6.12.14
 * Time: 1:45
 */

namespace Trejjam\Utils;

use Symfony\Component\Console\Command\Command;

abstract class CliHelper extends Command
{
	/**
	 * @var Labels
	 */
	protected $labels;

	public function __construct(Labels $labels) {
		parent::__construct();

		$this->labels = $labels;
	}
}