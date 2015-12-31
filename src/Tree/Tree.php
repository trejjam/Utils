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
	 * @param AItem[]                                   $allItems
	 * @param AItem[]                                   $rootItems
	 * @return AItem[][] (tree, list)
	 */
	public static function createTree($itemClass, $items, $properties = NULL, $allItems = [], $rootItems = [])
	{
		/** @var int[] $newItems */
		$newItems = [];

		foreach ($items as $v) {
			/** @var AItem $item */
			$item = new $itemClass($v, $properties);
			$newItems[] = $item->getId();
			$allItems[$item->getId()] = $item;
		}

		foreach ($newItems as $v) {
			$newItem = $allItems[$v];
			$newItem->connectToParent($allItems);

			if (!$newItem->hasParent()) {
				$rootItems[$newItem->getId()] = $newItem;
			}
		}

		return [$rootItems, $allItems];
	}

	/**
	 * @param IItem $child
	 * @param IItem $newParent
	 */
	public static function moveChild(IItem $child, IItem $newParent)
	{
		if ($child->hasParent()) {
			$child->getParent()->unlinkChild($child);
			$child->setParent($newParent);
		}
	}
}
