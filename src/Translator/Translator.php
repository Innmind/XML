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
     * @psalm-suppress ImpurePropertyFetch
     */
    private static function translateNode(\DOMNode $node): ?Node
    {
        if ($node->nodeType === \XML_COMMENT_NODE && $node instanceof \DOMComment) {
            return Node::comment($node->data);
        }

        if ($node->nodeType === \XML_TEXT_NODE && $node instanceof \DOMText) {
            return Node::text($node->data);
        }

        if ($node->nodeType === \XML_CDATA_SECTION_NODE && $node instanceof \DOMCharacterData) {
            return Node::characterData($node->data);
        }

        if ($node->nodeType === \XML_ENTITY_REF_NODE && $node instanceof \DOMEntityReference) {
            return Node::entityReference($node->nodeName);
        }

        if ($node->nodeType === \XML_PI_NODE && $node instanceof \DOMProcessingInstruction) {
            return Node::processingInstruction(
                $node->nodeName,
                $node->data,
            );
        }

        return null;
    }
}
