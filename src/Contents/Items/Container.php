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

	/**
	 * @param bool|FALSE $forceObject
	 * @return array|object
	 */
	public function getContent($forceObject = FALSE)
	{
		$out = [];

		foreach ($this->data as $k => $v) {
			$out[$k] = $v->getContent($forceObject);
		}

		return $forceObject ? Nette\Utils\ArrayHash::from($out) : $out;
	}

	/**
	 * @param bool|FALSE $forceObject
	 * @return array|object
	 */
	public function getRawContent($forceObject = FALSE)
	{
		$out = [];

		foreach ($this->data as $k => $v) {
			$out[$k] = $v->getRawContent($forceObject);
		}

		return $forceObject ? Nette\Utils\ArrayHash::from($out) : $out;
	}

	public function getRemovedItems()
	{
		$out = [];

		if (is_null($this->rawData)) {
			return NULL;
		}

		foreach (array_merge($this->rawData, $this->data) as $k => $v) {
			if ($v instanceof Container) {
				$tempSubRemoved = $v->getRemovedItems();

				if (is_array($tempSubRemoved) && count($tempSubRemoved) > 0) {
					$out[$k] = $tempSubRemoved;
				}
			}
			else if ($v instanceof Base) {
				//TODO
			}
			else {
				$out[$k] = $this->rawData[$k];
			}
		}

		return $out;
	}
}
