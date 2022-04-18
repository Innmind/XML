<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Exception\UnknownNodeType,
};
use Innmind\Immutable\Map;

/**
 * @psalm-immutable
 */
final class Translator
{
    /** @var Map<int, NodeTranslator> */
    private Map $translators;

    /**
     * @param Map<int, NodeTranslator> $translators
     */
    public function __construct(Map $translators)
    {
        $this->translators = $translators;
    }

    public function __invoke(\DOMNode $node): Node
    {
        return $this
            ->translators
            ->get($node->nodeType)
            ->match(
                fn($translate) => $translate($node, $this),
                static fn() => throw new UnknownNodeType($node->nodeName),
            );
    }

    /**
     * @psalm-pure
     */
    public static function default(): self
    {
        return new self(NodeTranslators::defaults());
    }
}
