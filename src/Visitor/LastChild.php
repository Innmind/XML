<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Exception\NodeDoesntHaveChildren,
};

/**
 * @psalm-immutable
 */
final class LastChild
{
    public function __invoke(Node $node): Node
    {
        return $node->children()->last()->match(
            static fn($node) => $node,
            static fn() => throw new NodeDoesntHaveChildren,
        );
    }
}
