<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\Translator,
    Document,
    Document\Type,
    Document\Version,
    Document\Encoding,
    Element,
    Node,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
    Predicate\Instance,
};

/**
 * @internal
 * @psalm-immutable
 */
final class DocumentTranslator
{
    private function __construct()
    {
    }

    /**
     * @return Maybe<Document>
     */
    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         */
        return Maybe::just($node)
            ->keep(Instance::of(\DOMDocument::class))
            ->flatMap(
                fn(\DOMDocument $node) => Maybe::all(
                    $this->buildVersion($node),
                    $this->buildChildren($node->childNodes, $translate),
                )->map(fn(Version $version, Sequence $children) => Document::of(
                    $version,
                    Maybe::of($node->doctype)->flatMap($this->buildDoctype(...)),
                    Maybe::of($node->encoding)->flatMap(Encoding::maybe(...)),
                    $children,
                )),
            );
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }

    /**
     * @return Maybe<Version>
     */
    private function buildVersion(\DOMDocument $document): Maybe
    {
        [$major, $minor] = \explode('.', $document->xmlVersion ?? '');

        return Version::maybe(
            (int) $major,
            (int) $minor,
        );
    }

    /**
     * @return Maybe<Type>
     */
    private function buildDoctype(\DOMDocumentType $type): Maybe
    {
        /** @psalm-suppress MixedArgument */
        return Type::maybe(
            $type->name,
            $type->publicId,
            $type->systemId,
        );
    }

    /**
     * @return Maybe<Sequence<Node|Element>>
     */
    private function buildChildren(
        \DOMNodeList $nodes,
        Translator $translate,
    ): Maybe {
        /** @var Sequence<Node|Element> */
        $translated = Sequence::of();

        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress ImpureMethodCall
         */
        return Sequence::of(...\array_values(\iterator_to_array($nodes)))
            ->keep(Instance::of(\DOMNode::class))
            ->exclude(static fn($child) => $child->nodeType === \XML_DOCUMENT_TYPE_NODE)
            ->sink($translated)
            ->maybe(
                static fn($translated, $child) => $translate($child)
                    ->keep(
                        Instance::of(Node::class)->or(
                            Instance::of(Element::class),
                        ),
                    )
                    ->map($translated),
            );
    }
}
