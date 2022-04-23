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
         * @psalm-suppress RedundantCondition
         * @psalm-suppress TypeDoesNotContainType
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMDocument)
            ->flatMap(
                fn(\DOMDocument $node) => $this
                    ->buildChildren($node->childNodes, $translate)
                    ->map(fn($children) => Document::of(
                        $this->buildVersion($node),
                        Maybe::of($node->doctype)->map($this->buildDoctype(...)),
                        Maybe::of($node->encoding)->map($this->buildEncoding(...)),
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

    private function buildVersion(\DOMDocument $document): Version
    {
        [$major, $minor] = \explode('.', $document->xmlVersion);

        return Version::of(
            (int) $major,
            (int) $minor,
        );
    }

    private function buildDoctype(\DOMDocumentType $type): Type
    {
        return Type::of(
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

    private function buildEncoding(string $encoding): Encoding
    {
        return Encoding::of($encoding);
    }
}
