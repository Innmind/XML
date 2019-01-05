<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NoNextSiblingException,
};

final class NextSibling
{
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function __invoke(Node $tree): Node
    {
        $parent = (new ParentNode($this->node))($tree);
        $position = $parent
            ->children()
            ->filter(function(int $position, Node $node) {
                return $node === $this->node;
            })
            ->key();

        if ($position === ($parent->children()->size() - 1)) {
            throw new NoNextSiblingException;
        }

        return $parent->children()->get($position + 1);
    }
}
