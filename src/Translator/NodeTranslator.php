<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    NodeInterface,
    Exception\UnknownNodeTypeException,
};
use Innmind\Immutable\MapInterface;

final class NodeTranslator
{
    private $translators;

    public function __construct(MapInterface $translators)
    {
        if (
            (string) $translators->keyType() !== 'int' ||
            (string) $translators->valueType() !== NodeTranslatorInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->translators = $translators;
    }

    public function translate(\DOMNode $node): NodeInterface
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
