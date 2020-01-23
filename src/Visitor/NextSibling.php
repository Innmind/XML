<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NoNextSibling,
};

final class NextSibling
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function __invoke(Node $tree): Node
    {
        $parent = (new ParentNode($this->node))($tree);
        $position = $parent
            ->children()
            ->values()
            ->indexOf($this->node);

        if ($position === ($parent->children()->size() - 1)) {
            throw new NoNextSibling;
        }

        return $parent->children()->get($position + 1);
    }
}
