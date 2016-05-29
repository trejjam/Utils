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
		ROW = '__row__',
		STRICT = '__strict__';

	/**
	 * @return Nette\Database\Table\Selection
	 */
	protected abstract function getTable();

	protected function prepareListQuery(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL, array $defaultFilterType = [], array $filterTranslate = [])
	{
		$query = $this->getTable();

		BaseQuery::appendSort($query, $sort);
		BaseQuery::appendFilter($query, $filter, $defaultFilterType, $filterTranslate, $defaultFilterType);
		BaseQuery::appendLimit($query, $limit, $offset);

		return $query;
	}

	/**
	 * @param array|NULL $sort
	 * @param array|NULL $filter
	 * @param int|null   $limit
	 * @param int|null   $offset
	 * @param array      $defaultFilterType
	 *
	 * @param array      $filterTranslate
	 *
	 * @return \stdClass[]
	 */
	public function getList(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL, array $defaultFilterType = [], array $filterTranslate = [])
	{
		$query = $this->prepareListQuery($sort, $filter, $limit, $offset, $defaultFilterType, $filterTranslate);

		$out = [];

		foreach ($query as $v) {
			$item = $this->getItem($v);
			$out[$item->id] = $item;
		}

		return $out;
	}

	/**
	 * @param int|\stdClass|Nette\Database\Table\IRow $id
	 *
	 * @return Nette\Database\Table\IRow
	 */
	protected function getRow($id)
	{
		if (isset($id->{static::ROW})) {
			$row = $id->{static::ROW};
		}
		else if ($id instanceof Nette\Database\Table\IRow) {
			$row = $id;
		}
		else {
			$row = $this->getTable()->get($id);
		}

		return $row;
	}

	/**
	 * @param int|\stdClass|Nette\Database\Table\IRow $id
	 *
	 * @return \stdClass
	 */
	public abstract function getItem($id);

	public function getCount(array $filter = NULL, array $defaultFilterType = [], array $filterTranslate = [])
	{
		$query = $this->getTable()->select('COUNT(*) count');

		BaseQuery::appendFilter($query, $filter, $defaultFilterType, $filterTranslate);

		return $query->fetch()->count;
	}

	/**
	 * @param \stdClass|Nette\Database\Table\IRow $row
	 * @param string|null                         $throughColumn
	 * @param array|NULL                          $sort
	 * @param array|NULL                          $filter
	 * @param int|null                            $limit
	 * @param int|null                            $offset
	 * @param array                               $defaultFilterType
	 *
	 * @return \stdClass[]
	 */
	public function getRelatedList($row, $throughColumn = NULL, array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL, array $defaultFilterType = [], array $filterTranslate = [])
	{
		if ($row instanceof \stdClass) {
			$row = $row->{static::ROW};
		}

		/** @var Nette\Database\Table\GroupedSelection $query */
		$query = $row->related($this->getTable()->getName(), $throughColumn);

		BaseQuery::appendSort($query, $sort);
		BaseQuery::appendFilter($query, $filter, $defaultFilterType, $filterTranslate);
		BaseQuery::appendLimit($query, $limit, $offset);

		$out = [];

		foreach ($query as $v) {
			$item = $this->getItem($v);
			$out[$item->id] = $item;
		}

		return $out;
	}
}
