<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\{
    Element\Name,
    Document\Type,
    Document\Version,
    Document\Encoding,
};
use Innmind\Immutable\{
    Maybe,
    Sequence,
    Set,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class Translator
{
    private function __construct(
    ) {
    }

    /**
     * @return Maybe<Document|Node|Element>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        return $this
            ->buildDocument($node)
            ->otherwise(fn() => $this->child($node));
    }

    /**
     * @psalm-pure
     */
    public static function default(): self
    {
        return new self();
    }

    /**
     * @return Maybe<Node|Element>
     */
    private function child(\DOMNode $node): Maybe
    {
        if ($node->nodeType === \XML_COMMENT_NODE && $node instanceof \DOMComment) {
            return Maybe::just(Node::comment($node->data));
        }

        if ($node->nodeType === \XML_TEXT_NODE && $node instanceof \DOMText) {
            return Maybe::just(Node::text($node->data));
        }

        if ($node->nodeType === \XML_CDATA_SECTION_NODE && $node instanceof \DOMCharacterData) {
            return Maybe::just(Node::characterData($node->data));
        }

        if ($node->nodeType === \XML_ENTITY_REF_NODE && $node instanceof \DOMEntityReference) {
            return Maybe::just(Node::entityReference($node->nodeName));
        }

        if ($node->nodeType === \XML_PI_NODE && $node instanceof \DOMProcessingInstruction) {
            return Maybe::just(Node::processingInstruction(
                $node->nodeName,
                $node->data,
            ));
        }

        if ($node->nodeType === \XML_ELEMENT_NODE && $node instanceof \DOMElement) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            return Maybe::all(
                Name::maybe($node->nodeName),
                self::attributes($node),
                $this->children(
                    Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                        ->keep(Instance::of(\DOMNode::class)),
                ),
            )->map(match ($node->childNodes->length) {
                0 => Element::selfClosing(...),
                default => Element::of(...),
            });
        }

        /** @var Maybe<Node|Element> */
        return Maybe::nothing();
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
                    $this->children(
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
     * @return Maybe<Set<Attribute>>
     */
    private static function attributes(\DOMElement $element): Maybe
    {
        /** @var Set<Attribute> */
        $attributes = Set::of();
        $attrs = [];

        if ($element->attributes instanceof \DOMNamedNodeMap) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            $attrs = \iterator_to_array($element->attributes);
        }

        return Sequence::of(...\array_values($attrs))
            ->keep(Instance::of(\DOMAttr::class))
            ->sink($attributes)
            ->maybe(
                static fn($attributes, $attribute) => Attribute::maybe(
                    $attribute->name,
                    $attribute->value,
                )->map($attributes),
            );
    }

    /**
     * @param Sequence<\DOMNode> $children
     *
     * @return Maybe<Sequence<Node|Element>>
     */
    private function children(Sequence $children): Maybe
    {
        /** @var Sequence<Node|Element> */
        $translated = Sequence::of();

        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress ImpureMethodCall
         */
        return $children
            ->sink($translated)
            ->maybe(
                fn($translated, $child) => $this
                    ->child($child)
                    ->map($translated),
            );
    }
}
