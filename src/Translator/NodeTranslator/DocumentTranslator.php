<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\Translator,
    Document,
    Document\Type,
    Document\Version,
    Document\Encoding,
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
                    Visitor\Children::of($translate)(
                        Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
                            ->keep(Instance::of(\DOMNode::class))
                            ->exclude(static fn($child) => $child->nodeType === \XML_DOCUMENT_TYPE_NODE),
                    ),
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
}
