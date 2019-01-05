<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\MapInterface;

interface Node
{
    /**
     * @return MapInterface<int, Node> The int represent the position
     */
    public function children(): MapInterface;
    public function hasChildren(): bool;
    public function removeChild(int $position): self;
    public function replaceChild(int $position, self $child): self;
    public function prependChild(self $child): self;
    public function appendChild(self $child): self;
    public function content(): string;
    public function __toString(): string;
}
