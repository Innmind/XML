<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Translator\NodeTranslator\Visitor\Attributes,
    Translator\NodeTranslator\Visitor\Children,
    Node,
    Element\SelfClosingElement,
    Element\Element,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class ElementTranslator implements NodeTranslator
{
    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /** @var Maybe<\DOMElement> */
        $node = Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMElement);

        /** @var Maybe<Node> */
        return $node
            ->filter(static fn($node) => $node->childNodes->length === 0)
            ->map(static fn($node) => new SelfClosingElement(
                $node->nodeName,
                (new Attributes)($node),
            ))
            ->otherwise(static fn() => $node->flatMap(
                static fn($node) => (new Children($translate))($node)->map(
                    static fn($children) => new Element(
                        $node->nodeName,
                        (new Attributes)($node),
                        ...$children->toList(),
                    ),
                ),
            ));
    }
}
