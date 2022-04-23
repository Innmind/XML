<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Node\Document,
};
use Innmind\Immutable\{
    Map,
    Sequence,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class DocumentTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress MixedArgumentTypeCoercion
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMDocument)
            ->flatMap(
                fn(\DOMDocument $node) => Maybe::all(
                    $this->buildVersion($node),
                    $this->buildChildren($node->childNodes, $translate),
                )->map(fn(Version $version, Sequence $children) => Document::of(
                    $version,
                    Maybe::of($node->doctype)->flatMap($this->buildDoctype(...)),
                    Maybe::of($node->encoding)->flatMap($this->buildEncoding(...)),
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
        [$major, $minor] = \explode('.', $document->xmlVersion);

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
        return Type::maybe(
            $type->name,
            $type->publicId,
            $type->systemId,
        );
    }

    /**
     * @return Maybe<Sequence<Node>>
     */
    private function buildChildren(
        \DOMNodeList $nodes,
        Translator $translate,
    ): Maybe {
        /** @var Maybe<Sequence<Node>> */
        $children = Maybe::just(Sequence::of());

        foreach ($nodes as $child) {
            if ($child->nodeType === \XML_DOCUMENT_TYPE_NODE) {
                continue;
            }

            $children = $children->flatMap(
                static fn($children) => $translate($child)->map(
                    static fn($node) => ($children)($node),
                ),
            );
        }

        return $children;
    }

    /**
     * @return Maybe<Encoding>
     */
    private function buildEncoding(string $encoding): Maybe
    {
        return Encoding::maybe($encoding);
    }
}
