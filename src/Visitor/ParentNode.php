<?php
declare(strict_types = 1);

namespace Innmind\XML\Visitor;

use Innmind\XML\{
    NodeInterface,
    Exception\NodeHasNoParentException
};

final class ParentNode
{
    private $node;

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function __invoke(NodeInterface $tree): NodeInterface
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
