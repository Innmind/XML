<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    NodeInterface,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class EntityReference implements NodeInterface
{
    private $data;
    private $children;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->children = new Map('int', NodeInterface::class);
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

    public function removeChild(int $position): NodeInterface
    {
        throw new LogicException;
    }

    public function replaceChild(int $position, NodeInterface $node): NodeInterface
    {
        throw new LogicException;
    }

    public function prependChild(NodeInterface $child): NodeInterface
    {
        throw new LogicException;
    }

    public function appendChild(NodeInterface $child): NodeInterface
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
