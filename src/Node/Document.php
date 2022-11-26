<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    AsContent,
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
final class Document implements Node, AsContent
{
    private Version $version;
    /** @var Maybe<Type> */
    private Maybe $type;
    /** @var Maybe<Encoding> */
    private Maybe $encoding;
    /** @var Sequence<Node> */
    private Sequence $children;

    /**
     * @param Maybe<Type> $type
     * @param Maybe<Encoding> $encoding
     * @param Sequence<Node> $children
     */
    private function __construct(
        Version $version,
        Maybe $type,
        Maybe $encoding,
        Sequence $children,
    ) {
        $this->version = $version;
        $this->type = $type;
        $this->encoding = $encoding;
        $this->children = $children;
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<Type> $type
     * @param Maybe<Encoding> $encoding
     * @param Sequence<Node> $children
     */
    public static function of(
        Version $version,
        Maybe $type,
        Maybe $encoding,
        Sequence $children = null,
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

    public function children(): Sequence
    {
        return $this->children;
    }

    public function filterChild(callable $filter): self
    {
        return new self(
            $this->version,
            $this->type,
            $this->encoding,
            $this->children->filter($filter),
        );
    }

    public function mapChild(callable $map): self
    {
        return new self(
            $this->version,
            $this->type,
            $this->encoding,
            $this->children->map($map),
        );
    }

    public function prependChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = Sequence::lazyStartingWith($child)->append($this->children);

        return $document;
    }

    public function appendChild(Node $child): Node
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
            static fn(Node $child): string => $child->toString(),
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
        return Content\Lines::of(
            Sequence::lazyStartingWith(Content\Line::of(Str::of($this->tag())))
                ->append($this->type->match(
                    static fn($type) => Sequence::of(Content\Line::of(Str::of($type->toString()))),
                    static fn() => Sequence::of(),
                ))
                ->append(
                    $this
                        ->children
                        ->flatMap(static fn($node) => match ($node instanceof AsContent) {
                            true => $node->asContent()->lines(),
                            false => Content\Lines::ofContent($node->toString())->lines(),
                        }),
                ),
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
