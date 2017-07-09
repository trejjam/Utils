<?php

namespace Trejjam\Utils\Components;

use Trejjam;

interface IListingFactory
{
	/**
	 * @param Trejjam\Utils\Helpers\IBaseList $list
	 *
	 * @return ListingFactory
	 */
	function create(Trejjam\Utils\Helpers\IBaseList $list = NULL);
}
