<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class EntityReference implements Node
{
    private $data;
    private $children;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->children = new Map('int', Node::class);
    }

    /**
     * {@inheritdoc}
     */
    public function children(): MapInterface
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

    public function __toString(): string
    {
        return "&{$this->data};";
    }
}
