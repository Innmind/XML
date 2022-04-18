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

/**
 * @psalm-immutable
 */
final class TextTranslator implements NodeTranslator
{
    public function __invoke(
        \DOMNode $node,
        Translator $translate,
    ): Node {
        if (!$node instanceof \DOMText) {
            throw new InvalidArgumentException;
        }

        return new Text($node->data);
    }
}
