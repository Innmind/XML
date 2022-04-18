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

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node>
     */
    public function __invoke(Node $tree): Maybe
    {
        $children = (new ParentNode($this->node))($tree)->map(
            static fn($parent) => $parent->children(),
        );

        return $children
            ->flatMap(fn($children) => $children->indexOf($this->node))
            ->flatMap(static fn($position) => $children->flatMap(
                static fn($children) => $children->get($position - 1),
            ));
    }
}
