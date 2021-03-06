<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;

final class EntityReference implements Node
{
    private string $data;
    /** @var Sequence<Node> */
    private Sequence $children;

    public function __construct(string $data)
    {
        $this->data = $data;
        /** @var Sequence<Node> */
        $this->children = Sequence::of(Node::class);
    }

    public function children(): Sequence
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function removeChild(int $position): Node
    {
        throw new LogicException('Operation not applicable');
    }

    public function replaceChild(int $position, Node $child): Node
    {
        throw new LogicException('Operation not applicable');
    }

    public function prependChild(Node $child): Node
    {
        throw new LogicException('Operation not applicable');
    }

    public function appendChild(Node $child): Node
    {
        throw new LogicException('Operation not applicable');
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
