<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Map;

final class EntityReference implements Node
{
    private string $data;
    private Map $children;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->children = Map::of('int', Node::class);
    }

    /**
     * {@inheritdoc}
     */
    public function children(): Map
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function removeChild(int $position): Node
    {
        throw new LogicException;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        throw new LogicException;
    }

    public function prependChild(Node $child): Node
    {
        throw new LogicException;
    }

    public function appendChild(Node $child): Node
    {
        throw new LogicException;
    }

    public function content(): string
    {
        return $this->data;
    }

    public function toString(): string
    {
        return "&{$this->data};";
    }
}
