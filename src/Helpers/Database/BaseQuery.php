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
	static function appendFilter(Nette\Database\Table\Selection &$query, $filter = NULL, array $defaultFilterType = [], array $filterTranslate = [])
	{
		if ( !is_null($filter)) {
			if (isset($filter[ABaseList::STRICT])) {
				$query->where($filter[ABaseList::STRICT]);
				unset($filter[ABaseList::STRICT]);
			}

			foreach ($filter as $k => $v) {
				$key = $k;
				if (array_key_exists($k, $filterTranslate)) {
					$key = $filterTranslate[$k];
				}

				if (array_key_exists($k, $defaultFilterType)) {
					switch ($defaultFilterType[$k]) {
						case '<':
							$query->where($key . ' < ?', $v);

							break;
						case '<=':
							$query->where($key . ' <= ?', $v);

							break;

						case '<=date':
							$query->where($key . ' < ? + INTERVAL 1 DAY', $v);

							break;
						case '>':
							$query->where($key . ' > ?', $v);

							break;
						case '>=':
							$query->where($key . ' >= ?', $v);

							break;
						case '=':
							$query->where($key . ' = ?', $v);

							break;
						default:
							throw new Trejjam\Utils\LogicException('Unknown filter type ' . $defaultFilterType[$k]);
					}
				}
				else {
					$query->where(
						[
							$key . ' LIKE' => '%' . $v . '%',
						]
					);
				}
			}
		}

		return $query;
	}

	static function appendSort(Nette\Database\Table\Selection &$query, $sort = NULL)
	{
		if ( !is_null($sort)) {
			foreach ($sort as $k => $v) {
				if (Nette\Utils\Validators::isNumericInt($k)) {
					$query->order($v);
				}
				else {
					$query->order($k . ' ' . strtoupper($v));
				}
			}
		}

		return $query;
	}

	static function appendLimit(Nette\Database\Table\Selection &$query, $limit = NULL, $offset = NULL)
	{
		if ( !is_null($limit)) {
			$query->limit($limit, $offset);
		}

		return $query;
	}
}
