<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
};
use Innmind\Immutable\{
    Maybe,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class EntityReferenceTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    #[\Override]
    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        return Maybe::just($node)
            ->keep(Instance::of(\DOMEntityReference::class))
            ->map(static fn(\DOMEntityReference $node) => Node::entityReference($node->nodeName));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
