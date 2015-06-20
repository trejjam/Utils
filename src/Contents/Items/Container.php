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

class Container extends Base
{
	/**
	 * @var Base[]
	 */
	protected $data = [];

	protected function sanitizeData($data)
	{
		if (!isset($this->configuration['child'])) {
			throw new Trejjam\Utils\DomainException('Container has not defined child.', Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
		}
		$child = $this->configuration['child'];

		$out = [];

		foreach ($child as $k => $v) {
			$out[$k] = Trejjam\Utils\Contents\Factory::getItemObject($v, isset($data[$k]) ? $data[$k] : NULL);
		}

		return $out;
	}

	/**
	 * @return Base[]
	 */
	public function getChild()
	{
		return $this->data;
	}
	public function getContent($forceObject = FALSE)
	{
		$out = [];

		foreach ($this->data as $k => $v) {
			$out[$k] = $v->getContent($forceObject);
		}

		return $forceObject ? (object)$out : $out;
	}
	public function getRawContent($forceObject = FALSE)
	{
		$out = [];

		foreach ($this->data as $k => $v) {
			$out[$k] = $v->getRawContent($forceObject);
		}

		return $forceObject ? (object)$out : $out;
	}
}
