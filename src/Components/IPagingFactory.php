<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 1.3.15
 * Time: 3:12
 */

namespace Trejjam\Utils\Components;

use Trejjam;

interface IPagingFactory
{
	/**
	 * @return PagingFactory
	 */
	function create();
}
