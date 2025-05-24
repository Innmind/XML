<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Element,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class ParentNode
{
    private Node|Element $node;

    private function __construct(Node|Element $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node|Element>
     */
    public function __invoke(Node|Element $tree): Maybe
    {
        /** @var Maybe<Node|Element> */
        $parent = Maybe::nothing();

        /** @var Maybe<Node|Element> */
        return $tree->children()->reduce(
            $parent,
            function(Maybe $parent, $child) use ($tree): Maybe {
                if ($child === $this->node) {
                    return Maybe::just($tree);
                }

                return $parent->otherwise(fn() => $this($child));
            },
        );
    }

    /**
     * @psalm-pure
     */
    public static function of(Node|Element $node): self
    {
        return new self($node);
    }
}
