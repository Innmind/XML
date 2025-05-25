<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Translator\NodeTranslator\Visitor\Attributes,
    Translator\NodeTranslator\Visitor\Children,
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
        return Maybe::just($node)
            ->keep(Instance::of(\DOMElement::class))
            ->flatMap(
                static fn($node) => Maybe::all(
                    Name::maybe($node->nodeName),
                    Attributes::of()($node),
                    Children::of($translate)(
                        Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                            ->keep(Instance::of(\DOMNode::class))
                    ),
                )->map(match ($node->childNodes->length) {
                    0 => Element::selfClosing(...),
                    default => Element::of(...),
                }),
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
