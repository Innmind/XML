<?php
declare(strict_types = 1);

namespace Innmind\XML;

use Innmind\Immutable\MapInterface;

interface ElementInterface extends NodeInterface
{
    public function name(): string;

    /**
     * @return MapInterface<string, AttributeInterface>
     */
    public function attributes(): MapInterface;
    public function hasAttributes(): bool;
    public function attribute(string $name): AttributeInterface;
}
