<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\Node;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class NextSibling
{
    private Node $node;

    private function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node>
     */
    public function __invoke(Node $tree): Maybe
    {
        return ParentNode::of($this->node)($tree)
            ->toSequence()
            ->flatMap(static fn($parent) => $parent->children())
            ->dropWhile(fn($node) => $node !== $this->node)
            ->drop(1)
            ->first();
    }

    /**
     * @psalm-pure
     */
    public static function of(Node $node): self
    {
        return new self($node);
    }
}
