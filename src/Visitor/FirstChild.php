<?php
declare(strict_types = 1);

namespace Innmind\XML\Visitor;

use Innmind\XML\{
    NodeInterface,
    Exception\NodeDoesntHaveChildrenException
};

final class FirstChild
{
    public function __invoke(NodeInterface $node): NodeInterface
    {
        if (!$node->hasChildren()) {
            throw new NodeDoesntHaveChildrenException;
        }

        return $node->children()->get(0);
    }
}
