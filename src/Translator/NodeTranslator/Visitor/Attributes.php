<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Attribute;
use Innmind\Immutable\{
    Set,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Attributes
{
    /**
     * @return Maybe<Set<Attribute>>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        /** @var Maybe<Set<Attribute>> */
        $attributes = Maybe::just(Set::of());

        if (!$node instanceof \DOMElement) {
            return $attributes;
        }

        foreach ($node->attributes as $name => $attribute) {
            $attributes = $attributes->flatMap(
                static fn($attributes) => Attribute::maybe($name, $attribute->value)->map(
                    static fn($attribute) => ($attributes)($attribute),
                ),
            );
        }

        return $attributes;
    }
}
