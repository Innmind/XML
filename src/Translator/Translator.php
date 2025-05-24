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
        return ($this->document)($node, $this)
            ->otherwise(static fn() => Maybe::of(self::translateNode($node)))
            ->otherwise(
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

    /**
     * @psalm-pure
     */
    private static function translateNode(\DOMNode $node): ?Node
    {
        /** @psalm-suppress ImpurePropertyFetch */
        return match ($node->nodeType) {
            \XML_COMMENT_NODE => Node::comment($node->data),
            \XML_TEXT_NODE => Node::text($node->data),
            \XML_CDATA_SECTION_NODE => Node::characterData($node->data),
            \XML_ENTITY_REF_NODE => Node::entityReference($node->nodeName),
            \XML_PI_NODE => Node::processingInstruction(
                $node->nodeName,
                $node->data,
            ),
            default => null,
        };
    }
}
