<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Element,
    Document,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class NextSibling
{
    private Node|Element $node;

    private function __construct(Node|Element $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node|Element>
     */
    public function __invoke(Document|Node|Element $tree): Maybe
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
    public static function of(Node|Element $node): self
    {
        return new self($node);
    }
}
