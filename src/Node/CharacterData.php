<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;

final class CharacterData implements Node
{
    private string $value;
    private Sequence $children;

    public function __construct(string $value)
    {
        $this->value = $value;
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
        return $this->value;
    }

    public function toString(): string
    {
        return '<![CDATA['.$this->value.']]>';
    }
}
