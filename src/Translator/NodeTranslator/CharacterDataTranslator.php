<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Exception\InvalidArgumentException,
    Node\CharacterData,
};

final class CharacterDataTranslator implements NodeTranslator
{
    public function translate(
        \DOMNode $node,
        Translator $translator
    ): Node {
        if (!$node instanceof \DOMCharacterData) {
            throw new InvalidArgumentException;
        }

        return new CharacterData($node->data);
    }
}
