<?php
declare(strict_types = 1);

namespace Innmind\XML;

use Innmind\Immutable\MapInterface;

interface NodeInterface
{
    public function name(): string;

    /**
     * @return MapInterface<string, AttributeInterface>
     */
    public function attributes(): MapInterface;
    public function hasAttributes(): bool;
    public function attribute(string $name): AttributeInterface;

    /**
     * @return MapInterface<int, NodeInterface> The int represent the position
     */
    public function children(): MapInterface;
    public function hasChildren(): bool;
    public function content(): string;
    public function __toString(): string;
}
