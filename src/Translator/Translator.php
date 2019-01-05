<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Exception\UnknownNodeTypeException,
};
use Innmind\Immutable\MapInterface;

final class Translator
{
    private $translators;

    public function __construct(MapInterface $translators)
    {
        if (
            (string) $translators->keyType() !== 'int' ||
            (string) $translators->valueType() !== NodeTranslator::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->translators = $translators;
    }

    public function translate(\DOMNode $node): Node
    {
        if (!$this->translators->contains($node->nodeType)) {
            throw new UnknownNodeTypeException;
        }

        return $this
            ->translators
            ->get($node->nodeType)
            ->translate($node, $this);
    }
}
