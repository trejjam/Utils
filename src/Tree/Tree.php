<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 10.10.15
 * Time: 11:08
 */

namespace Trejjam\Utils\Tree;

use Nette,
	Trejjam;

class Tree
{
	/**
	 * @param string                                    $itemClass extends Trejjam\Utils\Tree\AItem or implement Trejjam\Utils\Tree\IItem
	 * @param array|\stdClass|Nette\Database\Table\IRow $items
	 * @param null|array                                $properties
	 * @return AItem[][] (tree, list)
	 */
	public static function createTree($itemClass, $items, $properties = NULL)
	{
		/** @var AItem[] $item */
		$allItems = [];
		/** @var AItem[] $rootItems */
		$rootItems = [];

		foreach ($items as $v) {
			/** @var AItem $item */
			$item = new $itemClass($v, $properties);
			$allItems[$item->getId()] = $item;
		}

		/** @var AItem $v */
		foreach ($allItems as $v) {
			$v->connectToParent($allItems);

			if (!$v->hasParent()) {
				$rootItems[$v->getId()] = $v;
			}
		}

		return [$rootItems, $allItems];
	}
}
