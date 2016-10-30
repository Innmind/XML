<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\NodeInterface;

/**
 * Extract whole text of a tree
 */
final class Text
{
    public function __invoke(NodeInterface $tree): string
    {
        if ($tree->hasChildren()) {
            return $tree
                ->children()
                ->reduce(
                    '',
                    function(string $string, int $position, NodeInterface $node): string {
                        return $string.$this($node);
                    }
                );
        }

        return $tree->content();
    }
}
