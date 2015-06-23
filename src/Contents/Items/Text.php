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
		$out = $this->rawData !== $this->data ? $this->rawData : NULL;

		list(, , $out) = $this->useSubType(function (SubType $subtype, array $inData) {
			list($data, $rawData, $previous) = $inData;

			return [
				$data,
				$rawData,
				$previous === FALSE ? $previous : $subtype->removedContent($rawData, $data),
			];
		}, [
			$this->data,
			$this->rawData,
			$out,
		]);

		return $out === FALSE ? NULL : $out;
	}
}
