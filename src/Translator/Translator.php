<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Exception\UnknownNodeType,
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
            throw new \TypeError(sprintf(
                'Argument 1 must be of type MapInterface<int, %s>',
                NodeTranslator::class
            ));
        }

        $this->translators = $translators;
    }

    public function __invoke(\DOMNode $node): Node
    {
        if (!$this->translators->contains($node->nodeType)) {
            throw new UnknownNodeType;
        }

        return $this
            ->translators
            ->get($node->nodeType)($node, $this);
    }
}
