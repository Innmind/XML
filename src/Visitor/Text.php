<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Element,
    Document,
};

/**
 * Extract whole text of a tree
 * @psalm-immutable
 */
final class Text
{
    private function __construct()
    {
    }

    public function __invoke(Document|Node|Element $tree): string
    {
        if ($tree instanceof Node) {
            return $tree->content();
        }

        return $tree->children()->match(
            fn($node, $children) => $children->reduce(
                $this($node),
                fn(string $string, $node) => $string.$this($node),
            ),
            static fn() => $tree->content(),
        );
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
