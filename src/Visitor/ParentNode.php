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
final class ParentNode
{
    private function __construct(
        private Node|Element|Custom $node,
    ) {
    }

    /**
     * @return Maybe<Element|Custom>
     */
    public function __invoke(Document|Node|Element|Custom $tree): Maybe
    {
        /** @var Maybe<Element|Custom> */
        $parent = Maybe::nothing();

        if ($tree instanceof Node) {
            return $parent;
        }

        if ($tree instanceof Custom) {
            $children = $tree->normalize()->children();
        } else {
            $children = $tree->children();
        }

        /** @var Maybe<Element|Custom> */
        return $children->reduce(
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
    public static function of(Node|Element|Custom $node): self
    {
        return new self($node);
    }
}
