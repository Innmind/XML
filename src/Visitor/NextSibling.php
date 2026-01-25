<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Element,
    Element\Custom,
    Document,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class NextSibling
{
    private function __construct(
        private Node|Element|Custom $node,
    ) {
    }

    /**
     * @return Maybe<Node|Element|Custom>
     */
    #[\NoDiscard]
    public function __invoke(Document|Node|Element|Custom $tree): Maybe
    {
        return ParentNode::of($this->node)($tree)
            ->toSequence()
            ->flatMap(static fn($parent) => match (true) {
                $parent instanceof Custom => $parent->normalize()->children(),
                default => $parent->children(),
            })
            ->dropWhile(fn($node) => $node !== $this->node)
            ->drop(1)
            ->first();
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(Node|Element|Custom $node): self
    {
        return new self($node);
    }
}
