<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\{
    Document\Type,
    Document\Version,
    Document\Encoding,
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
     * @param Sequence<Node|Element> $children
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
     * @param Sequence<Node|Element> $children
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
     * @return Sequence<Node|Element>
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    /**
     * @param callable(Node|Element): bool $filter
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
     * @param callable(Node|Element): (Node|Element) $map
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

    public function prependChild(Node|Element $child): self
    {
        $document = clone $this;
        $document->children = $this->children->prepend(Sequence::of($child));

        return $document;
    }

    public function appendChild(Node|Element $child): self
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

    public function content(): string
    {
        $children = $this->children->map(
            static fn($child) => $child->toString(),
        );

        return Str::of('')->join($children)->toString();
    }

    public function toString(): string
    {
        $string = $this->tag();

        $string .= $this->type->match(
            static fn($type) => "\n".$type->toString(),
            static fn() => '',
        );

        return $string."\n".$this->content();
    }

    public function asContent(): Content
    {
        return Content::ofLines(
            $this
                ->children
                ->flatMap(static fn($node) => $node->asContent()->lines())
                ->prepend($this->type->match(
                    static fn($type) => Sequence::of(Content\Line::of(Str::of($type->toString()))),
                    static fn() => Sequence::of(),
                ))
                ->prepend(Sequence::of(Content\Line::of(Str::of($this->tag())))),
        );
    }

    private function tag(): string
    {
        return \sprintf(
            '<?xml version="%s"%s?>',
            $this->version->toString(),
            $this
                ->encoding
                ->map(static fn($encoding) => ' encoding="'.$encoding->toString().'"')
                ->match(
                    static fn($encoding) => $encoding,
                    static fn() => '',
                ),
        );
    }
}
