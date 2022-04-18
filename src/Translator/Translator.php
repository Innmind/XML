<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Exception\UnknownNodeType,
};
use Innmind\Immutable\Map;

final class Translator
{
    private static ?self $default = null;

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

    public static function default(): self
    {
        return self::$default ??= new self(NodeTranslators::defaults());
    }
}
