<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;

final class Comment implements Node
{
    private string $value;
    /** @var Sequence<Node> */
    private Sequence $children;

    public function __construct(string $value)
    {
        $this->value = $value;
        /** @var Sequence<Node> */
        $this->children = Sequence::of(Node::class);
    }

    /**
     * {@inheritdoc}
     */
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
        return $this->value;
    }

    public function toString(): string
    {
        return '<!--'.$this->value.'-->';
    }
}
