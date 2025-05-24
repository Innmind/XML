<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Element,
    Document,
    Translator\NodeTranslator\DocumentTranslator,
};
use Innmind\Immutable\{
    Map,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Translator
{
    /**
     * @param Map<int, NodeTranslator> $translators
     */
    private function __construct(
        private DocumentTranslator $document,
        private Map $translators,
    ) {
    }

    /**
     * @return Maybe<Document|Node|Element>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        return ($this->document)($node, $this)->otherwise(
            fn() => $this
                ->translators
                ->get($node->nodeType)
                ->flatMap(fn($translate) => $translate($node, $this)),
        );
    }

    /**
     * @psalm-pure
     *
     * @param Map<int, NodeTranslator> $translators
     */
    public static function of(Map $translators): self
    {
        return new self(
            DocumentTranslator::of(),
            $translators,
        );
    }

    /**
     * @psalm-pure
     */
    public static function default(): self
    {
        return new self(
            DocumentTranslator::of(),
            NodeTranslators::defaults(),
        );
    }
}
