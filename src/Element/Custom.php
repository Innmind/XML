<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\Element;

/**
 * @psalm-immutable
 */
interface Custom
{
    #[\NoDiscard]
    public function normalize(): Element;
}
