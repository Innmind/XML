<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Attribute;
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class Attributes
{
    public function __invoke(\DOMNode $node): MapInterface
    {
        $attributes = new Map('string', Attribute::class);

        if (!$node instanceof \DOMElement) {
            return $attributes;
        }

        if (!$node->attributes) {
            return $attributes;
        }

        foreach ($node->attributes as $name => $attribute) {
            $attributes = $attributes->put(
                $name,
                new Attribute\Attribute(
                    $name,
                    $node->getAttribute($name)
                )
            );
        }

        return $attributes;
    }
}
