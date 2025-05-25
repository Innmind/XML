<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Attribute;
use Innmind\Immutable\{
    Set,
    Maybe,
    Sequence,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class Attributes
{
    private function __construct()
    {
    }

    /**
     * @return Maybe<Set<Attribute>>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        /** @var Set<Attribute> */
        $attributes = Set::of();

        if (!$node instanceof \DOMElement) {
            return Maybe::just($attributes);
        }

        $attrs = [];

        if ($node->attributes instanceof \DOMNamedNodeMap) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            $attrs = \iterator_to_array($node->attributes);
        }

        return Sequence::of(...\array_values($attrs))
            ->keep(Instance::of(\DOMAttr::class))
            ->sink($attributes)
            ->maybe(
                static fn($attributes, $attribute) => Attribute::maybe(
                    $attribute->name,
                    $attribute->value,
                )->map($attributes),
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
