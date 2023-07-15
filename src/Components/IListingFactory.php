<?php

namespace Trejjam\Utils\Components;

use Trejjam;

interface IListingFactory
{
    function create(Trejjam\Utils\Helpers\IBaseList|null $list = null): ListingFactory;
}
