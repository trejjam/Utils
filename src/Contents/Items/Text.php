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

	/**
	 * @param bool|FALSE $forceObject
	 * @return string
	 */
	public function getContent($forceObject = FALSE)
	{
		return $this->data;
	}

	/**
	 * @param bool|FALSE $forceObject
	 * @return mixed
	 */
	public function getRawContent($forceObject = FALSE)
	{
		return $this->rawData;
	}


	public function getRemovedItems()
	{
		return $this->rawData !== $this->data ? $this->rawData : NULL;
	}
}
