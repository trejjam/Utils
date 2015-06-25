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
	const
		LIST_BOX = '__list__';

	protected $removedData = [];

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

		/** @var Base[] $out */
		$out = $this->data;

		$i = 0;
		foreach (is_null($data) ? [] : $data as $k => $v) {
			if (
				!is_numeric($k) ||
				(!is_null($count) && $i >= $count) ||
				(!is_null($max) && $i >= $max)
			) {
				$this->removedData[$k] = $v;
				continue;
			}

			if (isset($out[$k])) {
				$out[$k]->update(isset($data[$k]) ? $data[$k] : NULL);
			}
			else {
				$out[$k] = Trejjam\Utils\Contents\Factory::getItemObject(['type' => 'container', 'child' => $child], $v, $this->subTypes);
			}

			$i++;
		}

		while (!is_null($count) && $i < $count) {
			$out[$i] = Trejjam\Utils\Contents\Factory::getItemObject(['type' => 'container', 'child' => $child], NULL, $this->subTypes);

			$i++;
		}

		return $out;
	}

	public function getRemovedItems()
	{
		if (!is_array($this->rawData)) {
			return $this->rawData;
		}
		else {
			$out = [];

			foreach ($this->data as $k => $v) {
				$tempSubRemoved = $v->getRemovedItems();

				if (!is_null($tempSubRemoved) && (!is_array($tempSubRemoved) || count($tempSubRemoved) > 0)) {
					$out[$k] = $tempSubRemoved;
				}
			}

			foreach ($this->removedData as $k => $v) {
				$out[$k] = $v;
			}

			return $out;
		}
	}

	/**
	 * @param Base|Container        $item
	 * @param Nette\Forms\Container $formContainer
	 * @param                       $name
	 * @param                       $parentName
	 * @param array                 $ids
	 * @param array                 $userOptions
	 */
	public function generateForm(Base $item, Nette\Forms\Container &$formContainer, $name, $parentName, array &$ids, array $userOptions = [])
	{
		$container = $formContainer->addContainer($name);

		if (!isset($item->configuration['count']) && (!isset($item->configuration['max']) || $item->configuration['max'] > count($item->getChild()))) {
			$new = $container->addSubmit($item::NEW_ITEM, $this->getConfigValue('addItemLabel', 'new', $userOptions));
			$new->setValidationScope(FALSE)
				->setAttribute('id', $ids[] = $parentName . '__' . $name . $item::NEW_ITEM);
		}

		$listSelect = $container->addSelect(self::LIST_BOX, $this->getConfigValue('listLabel', 'new', $userOptions));
		$items = [];

		$listHead = $this->getConfigValue('listHead', NULL, $userOptions);

		foreach ($this->getChild() as $childName => $child) {
			$subIds = [];
			$child->generateForm(
				$child,
				$container,
				$childName,
				$parentName . '__' . $name,
				$subIds,
				isset($userOptions['child']) && isset($userOptions['child'][$childName]) && is_array($userOptions['child'][$childName]) ? $userOptions['child'][$childName] : []
			);

			try {
				$itemName = is_null($listHead) ? $childName : Trejjam\Utils\Utils::getValue($child->getContent(), $listHead);
			}
			catch (Trejjam\Utils\LogicException $e) {
				if ($e->getCode() & Trejjam\Utils\Exception::UTILS_KEY_NOT_FOUND) {
					$itemName = $childName;
				}
				else {
					throw $e;
				}
			}

			if (empty($itemName)) {
				$itemName = $childName;
			}

			$items[$itemName] = $itemName;
		}

		$listSelect->setItems($items);

		$item->applyUserOptions($container, $userOptions);
	}
}
