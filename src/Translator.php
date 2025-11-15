<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\{
    Element\Name,
    Element\Custom,
    Document\Type,
    Document\Version,
    Document\Encoding,
};
use Innmind\Immutable\{
    Attempt,
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
     * @param pure-Closure(Element): Maybe<Custom> $custom
     */
    private function __construct(
        private \Closure $custom,
    ) {
    }

    /**
     * @return Attempt<Document|Node|Element|Custom>
     */
    public function __invoke(\Dom\Node $node): Attempt
    {
        return $this
            ->buildDocument($node)
            ->recover(fn() => $this->child($node));
    }

    /**
     * @psalm-pure
     *
     * @param ?pure-callable(Element): Maybe<Custom> $custom
     */
    public static function of(?callable $custom = null): self
    {
        /** @var Maybe<Custom> */
        $nothing = Maybe::nothing();

        return new self(\Closure::fromCallable(
            $custom ?? static fn() => $nothing,
        ));
    }

    /**
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress MixedPropertyFetch
     *
     * @return Attempt<Node|Element|Custom>
     */
    private function child(\Dom\Node $node): Attempt
    {
        if (
            $node->nodeType === \XML_COMMENT_NODE &&
            $node instanceof \Dom\Comment
        ) {
            return Attempt::result(Node::comment($node->data));
        }

        if (
            $node->nodeType === \XML_TEXT_NODE &&
            $node instanceof \Dom\Text
        ) {
            return Attempt::result(Node::text($node->data));
        }

        if (
            $node->nodeType === \XML_CDATA_SECTION_NODE &&
            $node instanceof \Dom\CharacterData
        ) {
            return Attempt::result(Node::characterData($node->data));
        }

        if (
            $node->nodeType === \XML_ENTITY_REF_NODE &&
            $node instanceof \Dom\EntityReference
        ) {
            return Attempt::result(Node::entityReference($node->nodeName));
        }

        if (
            $node->nodeType === \XML_PI_NODE &&
            $node instanceof \Dom\ProcessingInstruction
        ) {
            return Attempt::result(Node::processingInstruction(
                $node->nodeName,
                $node->data,
            ));
        }

        if (
            $node->nodeType === \XML_ELEMENT_NODE &&
            $node instanceof \Dom\Element
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            return Name::maybe($node->nodeName)
                ->attempt(static fn() => new \RuntimeException(\sprintf(
                    'Invalid node name "%s"',
                    $node->nodeName,
                )))
                ->flatMap(
                    fn($name) => self::attributes($node)->flatMap(
                        fn($attributes) => $this
                            ->children(
                                Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                                    ->keep(Instance::of(\Dom\Node::class)),
                            )
                            ->map(static fn($children) => match ($node->childNodes->length) {
                                0 => Element::selfClosing($name, $attributes),
                                default => Element::of($name, $attributes, $children),
                            }),
                    ),
                )
                ->map(fn($element) => ($this->custom)($element)->match(
                    static fn($custom) => $custom,
                    static fn() => $element,
                ));
        }

        /** @var Attempt<Node|Element> */
        return Attempt::error(new \RuntimeException(\sprintf(
            'Unknown node type %s',
            $node->nodeType,
        )));
    }

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress UndefinedPropertyFetch
     *
     * @return Attempt<Document>
     */
    private function buildDocument(\Dom\Node $node): Attempt
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return Maybe::just($node)
            ->keep(Instance::of(\Dom\Document::class))
            ->attempt(static fn() => new \RuntimeException('Not a document'))
            ->flatMap(
                fn($document) => self::buildVersion($document)
                    ->attempt(static fn() => new \RuntimeException('Inavlid document version'))
                    ->flatMap(
                        fn($version) => Maybe::just($document->encoding ?? $document->xmlEncoding ?? 'utf-8')
                            ->flatMap(Encoding::of(...))
                            ->attempt(static fn() => new \RuntimeException('Non supported document encoding'))
                            ->flatMap(
                                fn($encoding) => $this
                                    ->children(
                                        Sequence::of(...\array_values(\iterator_to_array($document->childNodes)))
                                            ->keep(Instance::of(\Dom\Node::class))
                                            ->exclude(static fn($child) => $child->nodeType === \XML_DOCUMENT_TYPE_NODE),
                                    )
                                    ->map(static fn($children) => Document::of(
                                        $version,
                                        Maybe::of($document->doctype)->flatMap(self::buildDoctype(...)),
                                        Maybe::just($encoding),
                                        $children,
                                    )),
                            ),
                    ),
            );
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpurePropertyFetch
     *
     * @return Maybe<Version>
     */
    private static function buildVersion(\Dom\Document $document): Maybe
    {
        [$major, $minor] = \explode('.', (string) ($document->xmlVersion ?? ''));

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
    private static function buildDoctype(\Dom\DocumentType $type): Maybe
    {
        /** @psalm-suppress MixedArgument */
        return Type::maybe(
            $type->name,
            $type->publicId,
            $type->systemId,
        );
    }

    /**
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress MixedArgument
     *
     * @return Attempt<Sequence<Attribute>>
     */
    private static function attributes(\Dom\Element $element): Attempt
    {
        /** @var Sequence<Attribute> */
        $attributes = Sequence::of();
        $attrs = [];

        if ($element->attributes instanceof \Dom\NamedNodeMap) {
            /**
             * @psalm-suppress ImpureFunctionCall
             * @psalm-suppress ImpureMethodCall
             */
            $attrs = \iterator_to_array($element->attributes);
        }

        return Sequence::of(...\array_values($attrs))
            ->keep(Instance::of(\Dom\Attr::class))
            ->sink($attributes)
            ->attempt(
                static fn($attributes, $attribute) => Attribute::maybe(
                    $attribute->name,
                    $attribute->value,
                )
                    ->attempt(static fn() => new \RuntimeException(\sprintf(
                        'Invalid attribute "%s" "%s"',
                        $attribute->name,
                        $attribute->value,
                    )))
                    ->map($attributes),
            );
    }

    /**
     * @psalm-suppress UndefinedDocblockClass Since the package still supports PHP 8.2
     *
     * @param Sequence<\Dom\Node> $children
     *
     * @return Attempt<Sequence<Node|Element|Custom>>
     */
    private function children(Sequence $children): Attempt
    {
        /** @var Sequence<Node|Element|Custom> */
        $translated = Sequence::of();

        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress ImpureMethodCall
         */
        return $children
            ->sink($translated)
            ->attempt(
                fn($translated, $child) => $this
                    ->child($child)
                    ->map($translated),
            );
    }
}
