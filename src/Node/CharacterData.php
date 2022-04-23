<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 */
final class CharacterData implements Node
{
    private string $value;
    /** @var Sequence<Node> */
    private Sequence $children;

    public function __construct(string $value)
    {
        $this->value = $value;
        /** @var Sequence<Node> */
        $this->children = Sequence::of();
    }

    public function children(): Sequence
    {
        return $this->children;
    }

    public function filterChild(callable $filter): self
    {
        return $this;
    }

    public function mapChild(callable $map): self
    {
        return $this;
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
        return '<![CDATA['.$this->value.']]>';
    }
}
