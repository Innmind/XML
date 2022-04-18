<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Node;
use Innmind\Immutable\{
    Map,
    Maybe,
};

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

    /**
     * @return Maybe<Node>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        return $this
            ->translators
            ->get($node->nodeType)
            ->flatMap(fn($translate) => $translate($node, $this));
    }

    /**
     * @psalm-pure
     */
    public static function default(): self
    {
        return new self(NodeTranslators::defaults());
    }
}
