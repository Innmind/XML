<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Translator\NodeTranslator\Visitor\Attributes,
    Translator\NodeTranslator\Visitor\Children,
    Node,
    Element,
    Element\Name,
};
use Innmind\Immutable\{
    Maybe,
    Sequence,
    Predicate\Instance,
};

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
                )->map(Element::selfClosing(...)),
            )
            ->otherwise(static fn() => $node->flatMap(
                static fn($node) => Maybe::all(
                    Name::maybe($node->nodeName),
                    Attributes::of()($node),
                    Children::of($translate)(
                        Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                            ->keep(Instance::of(\DOMNode::class))
                    ),
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
