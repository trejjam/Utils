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

class ListContainer extends Container
{
	protected function sanitizeData($data)
	{
		$count = isset($this->configuration['count']) ? $this->configuration['count'] : NULL;
		$max = isset($this->configuration['max']) ? $this->configuration['max'] : NULL;
		$child = isset($this->configuration['child'])
			? $this->configuration['child']
			: (isset($this->configuration['listItem']) ? $this->configuration['listItem'] : NULL);

		if (is_null($child)) {
			throw new Trejjam\Utils\DomainException('List has not defined child.', Trejjam\Utils\Exception::CONTENTS_INCOMPLETE_CONFIGURATION);
		}
		if (!is_null($count) && !is_null($max)) {
			throw new Trejjam\Utils\DomainException('List has defined \'count\' and \'max\' at same time.', Trejjam\Utils\Exception::CONTENTS_COLLISION_CONFIGURATION);
		}

		$out = [];

		$i = 0;
		foreach (is_null($data) ? [] : $data as $v) {
			if (
				(!is_null($count) && $i >= $count) ||
				(!is_null($max) && $i >= $max)
			) {
				continue;
			}

			$out[] = Trejjam\Utils\Contents\Factory::getItemObject(['type' => 'container', 'child' => $child], $v);

			$i++;
		}

		while (!is_null($count) && $i < $count) {
			$out[] = Trejjam\Utils\Contents\Factory::getItemObject(['type' => 'container', 'child' => $child], NULL);

			$i++;
		}

		return $out;
	}
}
