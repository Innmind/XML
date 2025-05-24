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
    Element\Name,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class ElementTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    #[\Override]
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
            ->flatMap(
                static fn($node) => Maybe::all(
                    Name::maybe($node->nodeName),
                    Attributes::of()($node),
                )->map(SelfClosingElement::of(...)),
            )
            ->otherwise(static fn() => $node->flatMap(
                static fn($node) => Maybe::all(
                    Name::maybe($node->nodeName),
                    Attributes::of()($node),
                    Children::of($translate)($node),
                )->map(Element::of(...)),
            ));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
