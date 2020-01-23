<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NodeHasNoParent,
};

final class ParentNode
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function __invoke(Node $tree): Node
    {
        $parent = $tree->children()->reduce(
            null,
            function(?Node $parent, int $index, Node $child) use ($tree): ?Node {
                if ($child === $this->node) {
                    return $tree;
                }

                try {
                    return $parent ?? $this($child);
                } catch (NodeHasNoParent $e) {
                    return null;
                }
            },
        );

        if ($parent instanceof Node) {
            return $parent;
        }

        throw new NodeHasNoParent;
    }
}
