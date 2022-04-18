<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\{
    Map,
    Maybe,
};

/**
 * @psalm-immutable
 */
interface Element extends Node
{
    public function name(): string;

    /**
     * @return Map<string, Attribute>
     */
    public function attributes(): Map;

    /**
     * @return Maybe<Attribute>
     */
    public function attribute(string $name): Maybe;
    public function removeAttribute(string $name): self;
    public function addAttribute(Attribute $attribute): self;
}
