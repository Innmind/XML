<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Node\Text,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class TextTranslator implements NodeTranslator
{
    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMText)
            ->map(static fn(\DOMText $node) => new Text($node->data));
    }
}
