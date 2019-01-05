<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Exception\InvalidArgumentException,
    Node\Comment,
};

final class CommentTranslator implements NodeTranslator
{
    public function translate(
        \DOMNode $node,
        Translator $translator
    ): Node {
        if (!$node instanceof \DOMComment) {
            throw new InvalidArgumentException;
        }

        return new Comment($node->data);
    }
}
