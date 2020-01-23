<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Attribute;
use Innmind\Immutable\Set;

final class Attributes
{
    public function __invoke(\DOMNode $node): Set
    {
        $attributes = Set::of(Attribute::class);

        if (!$node instanceof \DOMElement) {
            return $attributes;
        }

        if (!$node->attributes) {
            return $attributes;
        }

        foreach ($node->attributes as $name => $attribute) {
            $attributes = ($attributes)(
                new Attribute(
                    $name,
                    $node->getAttribute($name),
                ),
            );
        }

        return $attributes;
    }
}
