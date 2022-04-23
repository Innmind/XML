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
    /**
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * @return Map<non-empty-string, Attribute>
     */
    public function attributes(): Map;

    /**
     * @param non-empty-string $name
     *
     * @return Maybe<Attribute>
     */
    public function attribute(string $name): Maybe;

    /**
     * @param non-empty-string $name
     */
    public function removeAttribute(string $name): self;
    public function addAttribute(Attribute $attribute): self;
}
