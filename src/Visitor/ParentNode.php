<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\Node;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class ParentNode
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node>
     */
    public function __invoke(Node $tree): Maybe
    {
        /** @var Maybe<Node> */
        $parent = Maybe::nothing();

        /** @var Maybe<Node> */
        return $tree->children()->reduce(
            $parent,
            function(Maybe $parent, Node $child) use ($tree): Maybe {
                if ($child === $this->node) {
                    return Maybe::just($tree);
                }

                return $parent->otherwise(fn() => $this($child));
            },
        );
    }
}
