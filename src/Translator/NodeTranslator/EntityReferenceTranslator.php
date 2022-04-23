<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Node\EntityReference,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class EntityReferenceTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMEntityReference)
            ->map(static fn(\DOMEntityReference $node) => EntityReference::of($node->nodeName));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
