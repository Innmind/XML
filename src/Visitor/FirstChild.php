<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NodeDoesntHaveChildrenException,
};

final class FirstChild
{
    public function __invoke(Node $node): Node
    {
        if (!$node->hasChildren()) {
            throw new NodeDoesntHaveChildrenException;
        }

        return $node->children()->get(0);
    }
}
