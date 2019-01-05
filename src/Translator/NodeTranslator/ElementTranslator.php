<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Translator\NodeTranslator\Visitor\Attributes,
    Translator\NodeTranslator\Visitor\Children,
    NodeInterface,
    Exception\InvalidArgumentException,
    Element\SelfClosingElement,
    Element\Element,
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

        $attributes = (new Attributes)($node);

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
            (new Children($translator))($node)
        );
    }
}
