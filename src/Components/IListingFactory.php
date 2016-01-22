<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 18:28
 */

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
