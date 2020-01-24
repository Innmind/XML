<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\Map;

interface Element extends Node
{
    public function name(): string;

    /**
     * @return Map<string, Attribute>
     */
    public function attributes(): Map;
    public function attribute(string $name): Attribute;
    public function removeAttribute(string $name): self;
    public function addAttribute(Attribute $attribute): self;
}
