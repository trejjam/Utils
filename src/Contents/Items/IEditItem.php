<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 23.6.15
 * Time: 9:57
 */

namespace Trejjam\Utils\Contents\Items;

use Nette;

interface IEditItem
{
	public function generateForm(Base $item,  Nette\Forms\Container &$formContainer, $name, $parentName, array &$ids, array $userOptions = []);
}
