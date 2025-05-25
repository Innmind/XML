<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Element,
    Document,
    Document\Type,
    Document\Version,
    Document\Encoding,
    Translator\NodeTranslator\Visitor\Children,
};
use Innmind\Immutable\{
    Map,
    Maybe,
    Sequence,
    Predicate\Instance,
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
        private Map $translators,
    ) {
    }

    /**
     * @return Maybe<Document|Node|Element>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        return $this
            ->buildDocument($node)
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
            $translators,
        );
    }

    /**
     * @psalm-pure
     */
    public static function default(): self
    {
        return new self(
            NodeTranslators::defaults(),
        );
    }

    /**
     * @return Maybe<Document>
     */
    private function buildDocument(\DOMNode $node): Maybe
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return Maybe::just($node)
            ->keep(Instance::of(\DOMDocument::class))
            ->flatMap(
                fn(\DOMDocument $node) => Maybe::all(
                    self::buildVersion($node),
                    Children::of($this)(
                        Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                            ->keep(Instance::of(\DOMNode::class))
                            ->exclude(static fn($child) => $child->nodeType === \XML_DOCUMENT_TYPE_NODE),
                    ),
                )->map(static fn(Version $version, Sequence $children) => Document::of(
                    $version,
                    Maybe::of($node->doctype)->flatMap(self::buildDoctype(...)),
                    Maybe::of($node->encoding)->flatMap(Encoding::maybe(...)),
                    $children,
                )),
            );
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpurePropertyFetch
     *
     * @return Maybe<Version>
     */
    private static function buildVersion(\DOMDocument $document): Maybe
    {
        [$major, $minor] = \explode('.', $document->xmlVersion ?? '');

        return Version::maybe(
            (int) $major,
            (int) $minor,
        );
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpurePropertyFetch
     *
     * @return Maybe<Type>
     */
    private static function buildDoctype(\DOMDocumentType $type): Maybe
    {
        /** @psalm-suppress MixedArgument */
        return Type::maybe(
            $type->name,
            $type->publicId,
            $type->systemId,
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
