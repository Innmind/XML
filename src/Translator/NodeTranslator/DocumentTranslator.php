<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Exception\InvalidArgumentException,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Node\Document,
};
use Innmind\Immutable\Map;

final class DocumentTranslator implements NodeTranslator
{
    public function __invoke(
        \DOMNode $node,
        Translator $translate
    ): Node {
        if (!$node instanceof \DOMDocument) {
            throw new InvalidArgumentException;
        }

        return new Document(
            $this->buildVersion($node),
            $node->doctype ? $this->buildDoctype($node->doctype) : null,
            $node->childNodes ?
                $this->buildChildren($node->childNodes, $translate) : null,
            $node->encoding ? $this->buildEncoding($node->encoding) : null
        );
    }

    private function buildVersion(\DOMDocument $document): Version
    {
        list($major, $minor) = explode('.', $document->xmlVersion);

        return new Version(
            (int) $major,
            (int) $minor
        );
    }

    private function buildDoctype(\DOMDocumentType $type): Type
    {
        return new Type(
            $type->name,
            $type->publicId,
            $type->systemId
        );
    }

    private function buildChildren(
        \DOMNodeList $nodes,
        Translator $translate
    ): Map {
        $children = new Map('int', Node::class);

        foreach ($nodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                continue;
            }

            $children = $children->put(
                $children->size(),
                $translate($child)
            );
        }

        return $children;
    }

    private function buildEncoding(string $encoding): Encoding
    {
        return new Encoding($encoding);
    }
}
