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
                    ->map(fn($children) => new Document(
                        $this->buildVersion($node),
                        Maybe::of($node->doctype)->map($this->buildDoctype(...)),
                        Maybe::of($node->encoding)->map($this->buildEncoding(...)),
                        $children,
                    )),
            );
    }

    private function buildVersion(\DOMDocument $document): Version
    {
        [$major, $minor] = \explode('.', $document->xmlVersion);

        return new Version(
            (int) $major,
            (int) $minor,
        );
    }

    private function buildDoctype(\DOMDocumentType $type): Type
    {
        return new Type(
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
        return new Encoding($encoding);
    }
}
