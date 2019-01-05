<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NodeHasNoParentException,
};

final class ParentNode
{
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function __invoke(Node $tree): Node
    {
        if ($tree->hasChildren()) {
            foreach ($tree->children() as $child) {
                if ($child === $this->node) {
                    return $tree;
                }

                try {
                    return $this($child);
                } catch (NodeHasNoParentException $e) {
                    //pass
                }
            }
        }

        throw new NodeHasNoParentException;
    }
}
