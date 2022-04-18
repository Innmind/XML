<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NoPreviousSibling,
};

final class PreviousSibling
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function __invoke(Node $tree): Node
    {
        $parent = (new ParentNode($this->node))($tree);

        return $parent
            ->children()
            ->indexOf($this->node)
            ->filter(static fn($position) => $position > 0)
            ->flatMap(static fn($position) => $parent->children()->get($position - 1))
            ->match(
                static fn($node) => $node,
                static fn() => throw new NoPreviousSibling,
            );
    }
}
