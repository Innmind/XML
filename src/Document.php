<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\{
    Document\Type,
    Document\Version,
    Document\Encoding,
    Element\Custom,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Sequence,
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Document
{
    /**
     * @param Maybe<Type> $type
     * @param Maybe<Encoding> $encoding
     * @param Sequence<Node|Element|Custom> $children
     */
    private function __construct(
        private Version $version,
        private Maybe $type,
        private Maybe $encoding,
        private Sequence $children,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<Type> $type
     * @param Maybe<Encoding> $encoding
     * @param Sequence<Node|Element|Custom> $children
     */
    public static function of(
        Version $version,
        Maybe $type,
        Maybe $encoding,
        ?Sequence $children = null,
    ): self {
        return new self($version, $type, $encoding, $children ?? Sequence::of());
    }

    public function version(): Version
    {
        return $this->version;
    }

    /**
     * @return Maybe<Type>
     */
    public function type(): Maybe
    {
        return $this->type;
    }

    /**
     * @return Sequence<Node|Element|Custom>
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    /**
     * @param callable(Node|Element|Custom): bool $filter
     */
    public function filterChild(callable $filter): self
    {
        return new self(
            $this->version,
            $this->type,
            $this->encoding,
            $this->children->filter($filter),
        );
    }

    /**
     * @param callable(Node|Element|Custom): (Node|Element|Custom) $map
     */
    public function mapChild(callable $map): self
    {
        return new self(
            $this->version,
            $this->type,
            $this->encoding,
            $this->children->map($map),
        );
    }

    public function prependChild(Node|Element|Custom $child): self
    {
        $document = clone $this;
        $document->children = $this->children->prepend(Sequence::of($child));

        return $document;
    }

    public function appendChild(Node|Element|Custom $child): self
    {
        $document = clone $this;
        $document->children = ($this->children)($child);

        return $document;
    }

    /**
     * @return Maybe<Encoding>
     */
    public function encoding(): Maybe
    {
        return $this->encoding;
    }

    public function asContent(Format $format = Format::pretty): Content
    {
        $writer = new \XMLWriter;
        /** @psalm-suppress ImpureMethodCall */
        $writer->openMemory();

        if ($format === Format::pretty) {
            /** @psalm-suppress ImpureMethodCall */
            $writer->setIndent(true);
            /** @psalm-suppress ImpureMethodCall */
            $writer->setIndentString('    ');
        }

        return Content::ofChunks(
            $this->render($writer),
        );
    }

    /**
     * @return Sequence<Str>
     */
    private function render(\XMLWriter $writer): Sequence
    {
        /** @psalm-suppress ImpureMethodCall */
        $writer->startDocument(
            $this->version->toString(),
            $this->encoding->match(
                static fn($encoding) => $encoding->toString(),
                static fn() => null,
            ),
        );
        /** @psalm-suppress ImpureMethodCall */
        $_ = $this->type->match(
            static fn($type) => $writer->writeRaw($type->toString()."\n"),
            static fn() => null,
        );
        /** @psalm-suppress ImpureMethodCall */
        $tag = Sequence::of(Str::of($writer->outputMemory()));

        $children = $this->children->flatMap(
            static function($child) use ($writer) {
                if ($child instanceof Custom) {
                    $child = $child->normalize();
                }

                $write = \Closure::bind(
                    fn() => $this->render($writer),
                    $child,
                    $child::class,
                );

                /**
                 * @psalm-suppress PossiblyNullFunctionCall
                 * @var Sequence<Str>
                 */
                return $write();
            },
        );

        return $children->prepend($tag);
    }
}
