<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\Element;

/**
 * @psalm-immutable
 */
interface Custom
{
    public function normalize(): Element;
}
