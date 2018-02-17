<?php

namespace Trejjam\Utils\Components;

use Trejjam;

interface IListingFactory
{
	function create(Trejjam\Utils\Helpers\IBaseList $list = NULL) : ListingFactory;
}
