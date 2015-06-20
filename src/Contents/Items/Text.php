<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 19.6.15
 * Time: 5:30
 */

namespace Trejjam\Utils\Contents\Items;


use Nette,
	Trejjam;

class Text extends Base
{
	protected function sanitizeData($data)
	{
		return is_scalar($data) ? $data : '';
	}

	public function getContent($forceObject = FALSE)
	{
		return $this->data;
	}
	public function getRawContent($forceObject = FALSE)
	{
		return $this->rawData;
	}
}
