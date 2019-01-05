<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    NodeInterface,
    Exception\InvalidArgumentException,
    Node\CharacterData,
};

final class CharacterDataTranslator implements NodeTranslatorInterface
{
    public function translate(
        \DOMNode $node,
        NodeTranslator $translator
    ): NodeInterface {
        if (!$node instanceof \DOMCharacterData) {
            throw new InvalidArgumentException;
        }

        return new CharacterData($node->data);
    }
}
