<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    NodeInterface,
    Exception\InvalidArgumentException,
    Attribute,
    AttributeInterface,
    Element\SelfClosingElement,
    Element\Element
};
use Innmind\Immutable\Map;

final class ElementTranslator implements NodeTranslatorInterface
{
    public function translate(
        \DOMNode $node,
        NodeTranslator $translator
    ): NodeInterface {
        if (!$node instanceof \DOMElement) {
            throw new InvalidArgumentException;
        }

        $attributes = new Map('string', AttributeInterface::class);

        if ($node->attributes instanceof \DOMNamedNodeMap) {
            foreach ($node->attributes as $name => $attribute) {
                $attributes = $attributes->put(
                    $name,
                    new Attribute(
                        $name,
                        $attribute->childNodes->length === 1 ?
                            $attribute->childNodes->item(0)->nodeValue : ''
                    )
                );
            }
        }

        if ($node->childNodes->length === 0) {
            return new SelfClosingElement(
                $node->nodeName,
                $attributes
            );
        }

        if ($node->childNodes) {
            $children = new Map('int', NodeInterface::class);

            foreach ($node->childNodes as $child) {
                $children = $children->put(
                    $children->size(),
                    $translator->translate($child)
                );
            }
        }

        return new Element(
            $node->nodeName,
            $attributes,
            $children ?? null
        );
    }
}
