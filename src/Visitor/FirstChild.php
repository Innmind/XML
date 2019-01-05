<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NodeDoesntHaveChildren,
};

final class FirstChild
{
    public function __invoke(Node $node): Node
    {
        if (!$node->hasChildren()) {
            throw new NodeDoesntHaveChildren;
        }

        return $node->children()->get(0);
    }
}
