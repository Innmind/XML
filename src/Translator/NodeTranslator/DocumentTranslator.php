<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    NodeInterface,
    Exception\InvalidArgumentException,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Node\Document
};
use Innmind\Immutable\Map;

final class DocumentTranslator implements NodeTranslatorInterface
{
    public function translate(
        \DOMNode $node,
        NodeTranslator $translator
    ): NodeInterface {
        if (!$node instanceof \DOMDocument) {
            throw new InvalidArgumentException;
        }

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
                    $translator->translate($child)
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
}
