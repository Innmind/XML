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

        $attributes = $node->attributes ?
            $this->buildAttributes($node->attributes) : null;

        if (
            $node->childNodes instanceof \DOMNodeList &&
            $node->childNodes->length === 0
        ) {
            return new SelfClosingElement(
                $node->nodeName,
                $attributes
            );
        }

        return new Element(
            $node->nodeName,
            $attributes,
            $node->childNodes ?
                $this->buildChildren($node->childNodes, $translator) : null
        );
    }

    private function buildAttributes(\DOMNamedNodeMap $map): Map
    {
        $attributes = new Map('string', AttributeInterface::class);

        foreach ($map as $name => $attribute) {
            $attributes = $attributes->put(
                $name,
                new Attribute(
                    $name,
                    $attribute->childNodes->length === 1 ?
                        $attribute->childNodes->item(0)->nodeValue : ''
                )
            );
        }

        return $attributes;
    }

    private function buildChildren(
        \DOMNodeList $nodes,
        NodeTranslator $translator
    ): Map {
        $children = new Map('int', NodeInterface::class);

        foreach ($nodes as $child) {
            $children = $children->put(
                $children->size(),
                $translator->translate($child)
            );
        }

        return $children;
    }
}
