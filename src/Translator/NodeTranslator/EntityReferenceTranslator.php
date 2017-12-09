<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    NodeInterface,
    Exception\InvalidArgumentException,
    Node\EntityReference
};

final class EntityReferenceTranslator implements NodeTranslatorInterface
{
    public function translate(
        \DOMNode $node,
        NodeTranslator $translator
    ): NodeInterface {
        if (!$node instanceof \DOMEntityReference) {
            throw new InvalidArgumentException;
        }

        return new EntityReference($node->nodeName);
    }
}
