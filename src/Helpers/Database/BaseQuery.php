<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 17:18
 */

namespace Trejjam\Utils\Helpers\Database;


use Nette,
	Trejjam;

class BaseQuery
{
	static function appendFilter(Nette\Database\Table\Selection &$query, $filter = NULL)
	{
		if (!is_null($filter)) {
			foreach ($filter as $k => $v) {
				$query->where([
					$k . ' LIKE' => '%' . $v . '%',
				]);
			}
		}

		return $query;
	}
	static function appendSort(Nette\Database\Table\Selection &$query, $sort = NULL)
	{
		if (!is_null($sort)) {
			foreach ($sort as $k => $v) {
				$query->order($k . ' ' . strtoupper($v));
			}
		}

		return $query;
	}
	static function appendLimit(Nette\Database\Table\Selection &$query, $limit = NULL, $offset = NULL)
	{
		if (!is_null($limit)) {
			$query->limit($limit, $offset);
		}

		return $query;
	}
}
