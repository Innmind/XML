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
        return $node->children()->first()->match(
            static fn($node) => $node,
            static fn() => throw new NodeDoesntHaveChildren,
        );
    }
}
