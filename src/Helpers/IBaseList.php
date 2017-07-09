<?php

namespace Trejjam\Utils\Helpers;

use Nette;
use Trejjam;

interface IBaseList
{
	function getList(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL, array $defaultFilterType = [], array $filterTranslate = []);
	function getItem($id);
	function getCount(array $filter = NULL, array $defaultFilterType = [], array $filterTranslate = []);
}
