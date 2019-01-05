<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Exception\InvalidArgumentException,
    Node\Text,
};

final class TextTranslator implements NodeTranslator
{
    public function translate(
        \DOMNode $node,
        Translator $translator
    ): Node {
        if (!$node instanceof \DOMText) {
            throw new InvalidArgumentException;
        }

        return new Text($node->data);
    }
}
