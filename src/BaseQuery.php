<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 17:18
 */

namespace Trejjam\Utils;


use Nette,
	Trejjam;

class BaseQuery
{
	static function appendFilter(Nette\Database\Table\Selection &$query, $filter = NULL)
	{
		if (!is_null($filter)) {
			foreach ($filter as $k => $v) {
				$query = $query->where([
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
				$query = $query->order($k . ' ' . strtoupper($v));
			}
		}

		return $query;
	}
	static function appendLimit(Nette\Database\Table\Selection &$query, $limit = NULL, $offset = NULL)
	{
		if (!is_null($limit)) {
			$query = $query->limit($limit, $offset);
		}

		return $query;
	}
}
