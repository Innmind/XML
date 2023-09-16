<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\Node;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class PreviousSibling
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
        $children = ParentNode::of($this->node)($tree)->map(
            static fn($parent) => $parent->children(),
        );

        /** @psalm-suppress InvalidArgument */
        return $children
            ->flatMap(fn($children) => $children->indexOf($this->node))
            ->filter(static fn($position) => $position >= 0)
            ->flatMap(static fn($position) => $children->flatMap(
                static fn($children) => $children->get($position - 1),
            ));
    }

    /**
     * @psalm-pure
     */
    public static function of(Node $node): self
    {
        return new self($node);
    }
}
