<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\MapInterface;

interface Element extends Node
{
    public function name(): string;

    /**
     * @return MapInterface<string, Attribute>
     */
    public function attributes(): MapInterface;
    public function hasAttributes(): bool;
    public function attribute(string $name): Attribute;
    public function removeAttribute(string $name): self;
    public function replaceAttribute(Attribute $attribute): self;
    public function addAttribute(Attribute $attribute): self;
}
