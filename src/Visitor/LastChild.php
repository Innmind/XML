<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    NodeInterface,
    Exception\NodeDoesntHaveChildrenException,
};

final class LastChild
{
    public function __invoke(NodeInterface $node): NodeInterface
    {
        if (!$node->hasChildren()) {
            throw new NodeDoesntHaveChildrenException;
        }

        return $node->children()->get(
            $node->children()->size() - 1
        );
    }
}
