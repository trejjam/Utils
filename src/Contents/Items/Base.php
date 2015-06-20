<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 5:26
 */

namespace Trejjam\Utils\Contents\Items;


use Nette,
	Trejjam;

abstract class Base
{
	protected $data;
	protected $rawData;
	protected $configuration;

	function __construct($configuration, $data = NULL)
	{
		$this->configuration = $configuration;
		$this->rawData = $data;
		$this->data = $this->sanitizeData($data);
	}

	abstract protected function sanitizeData($data);
	abstract public function getContent($forceObject = FALSE);
	abstract public function getRawContent($forceObject = FALSE);
}
