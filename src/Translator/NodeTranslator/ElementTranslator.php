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
use Innmind\Immutable\{
    Maybe,
    Set,
    Sequence,
};

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

        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress MixedArgument
         * @var Maybe<Node>
         */
        return $node
            ->filter(static fn($node) => $node->childNodes->length === 0)
            ->flatMap(static fn($node) => (new Attributes)($node)->map(
                static fn($attributes) => new SelfClosingElement(
                    $node->nodeName,
                    $attributes,
                ),
            ))
            ->otherwise(static fn() => $node->flatMap(
                static fn($node) => Maybe::all((new Attributes)($node), (new Children($translate))($node))->map(
                    static fn(Set $attributes, Sequence $children) => new Element(
                        $node->nodeName,
                        $attributes,
                        $children,
                    ),
                ),
            ));
    }
}
