<?php
declare(strict_types=1);

namespace Trejjam\Utils\Components;

use Nette;

interface IRenderable
{
    /**
     * @param \stdClass[] $list
     */
    function render(\stdClass|null $parameter = NULL, array $list = []): Nette\Utils\Html;
}
