<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 17:14
 */

namespace Trejjam\Utils;

use Nette,
	Trejjam;

abstract class DatabaseBaseList implements BaseList
{
	/**
	 * @return Nette\Database\Table\Selection
	 */
	protected abstract function getTable();
	public function getList(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL) {
		$query = $this->getTable();

		Trejjam\Utils\BaseQuery::appendSort($query, $sort);
		Trejjam\Utils\BaseQuery::appendFilter($query, $filter);
		Trejjam\Utils\BaseQuery::appendLimit($query, $limit, $offset);

		$out = [];

		foreach ($query as $v) {
			$item = $this->getItem($v);
			$out[$item->id] = $item;
		}

		return $out;
	}
	/**
	 * @param int|Nette\Database\Table\IRow $id
	 * @return \stdClass
	 */
	public abstract function getItem($id);
	public function getCount(array $filter = NULL) {
		$query = $this->getTable()->select('COUNT(*) count');

		Trejjam\Utils\BaseQuery::appendFilter($query, $filter);

		return $query->fetch()->count;
	}
}
