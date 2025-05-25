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
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     *
     * @return Maybe<Document|Node|Element>
     */
    public function __invoke(\DOMNode|\Dom\Node $node): Maybe
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
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     * @psalm-suppress TypeDoesNotContainType
     *
     * @return Maybe<Node|Element>
     */
    private function child(\DOMNode|\Dom\Node $node): Maybe
    {
        if (
            $node->nodeType === \XML_COMMENT_NODE &&
            (
                $node instanceof \DOMComment ||
                $node instanceof \Dom\Comment
            )
        ) {
            return Maybe::just(Node::comment($node->data));
        }

        if (
            $node->nodeType === \XML_TEXT_NODE &&
            (
                $node instanceof \DOMText ||
                $node instanceof \Dom\Text
            )
        ) {
            return Maybe::just(Node::text($node->data));
        }

        if (
            $node->nodeType === \XML_CDATA_SECTION_NODE &&
            (
                $node instanceof \DOMCharacterData ||
                $node instanceof \Dom\CharacterData
            )
        ) {
            return Maybe::just(Node::characterData($node->data));
        }

        if (
            $node->nodeType === \XML_ENTITY_REF_NODE &&
            (
                $node instanceof \DOMEntityReference ||
                $node instanceof \Dom\EntityReference
            )
        ) {
            return Maybe::just(Node::entityReference($node->nodeName));
        }

        if (
            $node->nodeType === \XML_PI_NODE &&
            (
                $node instanceof \DOMProcessingInstruction ||
                $node instanceof \Dom\ProcessingInstruction
            )
        ) {
            return Maybe::just(Node::processingInstruction(
                $node->nodeName,
                $node->data,
            ));
        }

        if (
            $node->nodeType === \XML_ELEMENT_NODE &&
            (
                $node instanceof \DOMElement ||
                $node instanceof \Dom\Element
            )
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            return Maybe::all(
                Name::maybe($node->nodeName),
                self::attributes($node),
                $this->children(
                    Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                        ->keep(
                            Instance::of(\DOMNode::class)->or(
                                Instance::of(\Dom\Node::class),
                            ),
                        ),
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
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     *
     * @return Maybe<Document>
     */
    private function buildDocument(\DOMNode|\Dom\Node $node): Maybe
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return Maybe::just($node)
            ->keep(
                Instance::of(\DOMDocument::class)->or(
                    Instance::of(\Dom\Document::class),
                ),
            )
            ->flatMap(
                fn($document) => Maybe::all(
                    self::buildVersion($document),
                    $this->children(
                        Sequence::of(...\array_values(\iterator_to_array($document->childNodes)))
                            ->keep(
                                Instance::of(\DOMNode::class)->or(
                                    Instance::of(\Dom\Node::class),
                                ),
                            )
                            ->exclude(static fn($child) => $child->nodeType === \XML_DOCUMENT_TYPE_NODE),
                    ),
                )->map(static fn(Version $version, Sequence $children) => Document::of(
                    $version,
                    Maybe::of($document->doctype)->flatMap(self::buildDoctype(...)),
                    Maybe::of(
                        $document->encoding ?? $document->xmlEncoding,
                    )->flatMap(Encoding::maybe(...)),
                    $children,
                )),
            );
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpurePropertyFetch
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     *
     * @return Maybe<Version>
     */
    private static function buildVersion(\DOMDocument|\Dom\Document $document): Maybe
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
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     *
     * @return Maybe<Type>
     */
    private static function buildDoctype(\DOMDocumentType|\Dom\DocumentType $type): Maybe
    {
        /** @psalm-suppress MixedArgument */
        return Type::maybe(
            $type->name,
            $type->publicId,
            $type->systemId,
        );
    }

    /**
     * @psalm-suppress UndefinedClass Since the package still supports PHP 8.2
     * @psalm-suppress TypeDoesNotContainType
     *
     * @return Maybe<Set<Attribute>>
     */
    private static function attributes(\DOMElement|\Dom\Element $element): Maybe
    {
        /** @var Set<Attribute> */
        $attributes = Set::of();
        $attrs = [];

        if (
            $element->attributes instanceof \DOMNamedNodeMap ||
            $element->attributes instanceof \Dom\NamedNodeMap
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             * @psalm-suppress PossiblyInvalidArgument Due to \Dom\NamedNodeMap for PHP 8.2
             */
            $attrs = \iterator_to_array($element->attributes);
        }

        return Sequence::of(...\array_values($attrs))
            ->keep(
                Instance::of(\DOMAttr::class)->or(
                    Instance::of(\Dom\Attr::class),
                ),
            )
            ->sink($attributes)
            ->maybe(
                static fn($attributes, $attribute) => Attribute::maybe(
                    $attribute->name,
                    $attribute->value,
                )->map($attributes),
            );
    }

    /**
     * @psalm-suppress UndefinedDocblockClass Since the package still supports PHP 8.2
     *
     * @param Sequence<\DOMNode|\Dom\Node> $children
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
