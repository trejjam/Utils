<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 10.10.15
 * Time: 11:09
 */

namespace Trejjam\Utils\Tree;

use Nette,
	Trejjam;

interface IItem
{
	/**
	 * IItem constructor.
	 * @param array|\stdClass|Nette\Database\Table\IRow $properties
	 * @param array|NULL                                $persistProperties
	 */
	public function __construct($properties, array $persistProperties = NULL);

	/**
	 * @param IItem[] $allItems
	 *
	 * @internal
	 */
	public function connectToParent(array $allItems);

	/**
	 * @param IItem $child
	 *
	 * @internal
	 */
	public function connectChild(IItem $child);

	public function getId();

	/**
	 * @return bool
	 */
	public function hasParent();
	/**
	 * @return bool
	 */
	public function hasChild();

	/**
	 * @return null|static
	 */
	public function getParent();
	/**
	 * @return static[]
	 */
	public function getChild();

	/**
	 * @return static[]
	 */
	public function createRootWay();

	/**
	 * @return array|\stdClass|Nette\Database\Table\IRow
	 */
	public function getProperties();

	public function __get($name);

	public function __isset($name);
}
