<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 17:14
 */

namespace Trejjam\Utils\Helpers\Database;

use Nette,
	Trejjam;

abstract class ABaseList implements Trejjam\Utils\Helpers\IBaseList
{
	const
		ROW = '__row__';

	/**
	 * @return Nette\Database\Table\Selection
	 */
	protected abstract function getTable();

	protected function prepareListQuery(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL)
	{
		$query = $this->getTable();

		BaseQuery::appendSort($query, $sort);
		BaseQuery::appendFilter($query, $filter);
		BaseQuery::appendLimit($query, $limit, $offset);

		return $query;
	}

	/**
	 * @param array|NULL $sort
	 * @param array|NULL $filter
	 * @param int|null   $limit
	 * @param int|null   $offset
	 * @return \stdClass[]
	 */
	public function getList(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL)
	{
		$query = $this->prepareListQuery($sort, $filter, $limit, $offset);

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

	public function getCount(array $filter = NULL)
	{
		$query = $this->getTable()->select('COUNT(*) count');

		BaseQuery::appendFilter($query, $filter);

		return $query->fetch()->count;
	}

	/**
	 * @param \stdClass|Nette\Database\Table\IRow $row
	 * @param string|null                         $throughColumn
	 * @param array|NULL                          $sort
	 * @param array|NULL                          $filter
	 * @param int|null                            $limit
	 * @param int|null                            $offset
	 * @return \stdClass[]
	 */
	public function getRelatedList($row, $throughColumn = NULL, array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL)
	{
		if ($row instanceof \stdClass) {
			$row = $row->{self::ROW};
		}

		$query = $row->related($this->getTable()->getName(), $throughColumn);

		BaseQuery::appendSort($query, $sort);
		BaseQuery::appendFilter($query, $filter);
		BaseQuery::appendLimit($query, $limit, $offset);

		$out = [];

		foreach ($query as $v) {
			$item = $this->getItem($v);
			$out[$item->id] = $item;
		}

		return $out;
	}
}
