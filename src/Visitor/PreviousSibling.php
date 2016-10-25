<?php
declare(strict_types = 1);

namespace Innmind\XML\Visitor;

use Innmind\XML\{
    NodeInterface,
    Exception\NoPreviousSiblingException
};

final class PreviousSibling
{
    private $node;

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function __invoke(NodeInterface $tree): NodeInterface
    {
        $parent = (new ParentNode($this->node))($tree);
        $position = $parent
            ->children()
            ->filter(function (int $position, NodeInterface $node) {
                return $node === $this->node;
            })
            ->key();

        if ($position === 0) {
            throw new NoPreviousSiblingException;
        }

        return $parent->children()->get($position - 1);
    }
}
