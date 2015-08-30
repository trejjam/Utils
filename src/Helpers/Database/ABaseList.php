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

		IBaseQuery::appendSort($query, $sort);
		IBaseQuery::appendFilter($query, $filter);
		IBaseQuery::appendLimit($query, $limit, $offset);

		return $query;
	}

	/**
	 * @param array|NULL $sort
	 * @param array|NULL $filter
	 * @param int|null       $limit
	 * @param int|null       $offset
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

		IBaseQuery::appendFilter($query, $filter);

		return $query->fetch()->count;
	}
}
