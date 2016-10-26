<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    NodeInterface,
    Node\CharacterData,
    Node\Text,
    Node\Comment,
    Node\Document,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Element\SelfClosingElement,
    Element\Element,
    AttributeInterface,
    Attribute,
    Exception\UnknownNodeTypeException
};
use Innmind\Immutable\Map;

final class NodeTranslator implements NodeTranslatorInterface
{
    public function translate(\DOMNode $node): NodeInterface
    {
        switch ($node->nodeType) {
            case XML_DOCUMENT_NODE:
                return $this->createDocument($node);
            case XML_ELEMENT_NODE:
                return $this->createElement($node);
            case XML_CDATA_SECTION_NODE:
                return new CharacterData($node->data);
            case XML_TEXT_NODE:
                return new Text($node->data);
            case XML_COMMENT_NODE:
                return new Comment($node->data);
        }

        throw new UnknownNodeTypeException;
    }

    private function createDocument(\DOMDocument $node): Document
    {
        list($major, $minor) = explode('.', $node->xmlVersion);
        $type = $children = $encoding = null;

        if ($node->doctype) {
            $type = new Type(
                $node->doctype->name,
                $node->doctype->publicId,
                $node->doctype->systemId
            );
        }

        if ($node->childNodes) {
            $children = new Map('int', NodeInterface::class);

            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    continue;
                }

                $children = $children->put(
                    $children->size(),
                    $this->translate($child)
                );
            }
        }

        if ($node->encoding) {
            $encoding = new Encoding($node->encoding);
        }

        return new Document(
            new Version((int) $major, (int) $minor),
            $type,
            $children,
            $encoding
        );
    }

    private function createElement(\DOMElement $element): NodeInterface
    {
        $attributes = new Map('string', AttributeInterface::class);

        if ($element->attributes instanceof \DOMNamedNodeMap) {
            foreach ($element->attributes as $name => $attribute) {
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

        if ($element->childNodes->length === 0) {
            return new SelfClosingElement(
                $element->nodeName,
                $attributes
            );
        }

        return new Element(
            $element->nodeName,
            $attributes,
            $this->createChildren($element->childNodes)
        );
    }

    private function createChildren(\DOMNodeList $nodes): Map
    {
        $children = new Map('int', NodeInterface::class);

        foreach ($nodes as $node) {
            $children = $children->put(
                $children->size(),
                $this->translate($node)
            );
        }

        return $children;
    }
}
