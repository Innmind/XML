<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Exception\UnknownNodeType,
};
use Innmind\Immutable\Map;
use function Innmind\Immutable\assertMap;

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
        assertMap('int', NodeTranslator::class, $translators, 1);

        $this->translators = $translators;
    }

    public static function default(): self
    {
        return self::$default ??= new self(NodeTranslators::defaults());
    }

    public function __invoke(\DOMNode $node): Node
    {
        if (!$this->translators->contains($node->nodeType)) {
            throw new UnknownNodeType($node->nodeName);
        }

        return $this
            ->translators
            ->get($node->nodeType)($node, $this);
    }
}
