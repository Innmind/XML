<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Exception\OutOfBoundsException,
};
use Innmind\Immutable\{
    Sequence,
    Str,
};

/**
 * @psalm-immutable
 */
final class Document implements Node
{
    private Version $version;
    private ?Type $type = null;
    /** @var Sequence<Node> */
    private Sequence $children;
    private ?Encoding $encoding = null;

    /**
     * @param Sequence<Node> $children
     */
    public function __construct(
        Version $version,
        Type $type = null,
        Encoding $encoding = null,
        Sequence $children = null,
    ) {
        $this->version = $version;
        $this->type = $type;
        $this->encoding = $encoding;
        $this->children = $children ?? Sequence::of();
    }

    public function version(): Version
    {
        return $this->version;
    }

    /** @psalm-suppress InvalidNullableReturnType */
    public function type(): Type
    {
        /** @psalm-suppress NullableReturnStatement */
        return $this->type;
    }

    public function hasType(): bool
    {
        return $this->type instanceof Type;
    }

    public function children(): Sequence
    {
        return $this->children;
    }

    public function removeChild(int $position): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException((string) $position);
        }

        $document = clone $this;
        /** @psalm-suppress ArgumentTypeCoercion */
        $document->children = $this
            ->children
            ->take($position)
            ->append($this->children->drop($position + 1));

        return $document;
    }

    public function replaceChild(int $position, Node $child): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException((string) $position);
        }

        $document = clone $this;
        /** @psalm-suppress ArgumentTypeCoercion */
        $document->children = $this
            ->children
            ->take($position)
            ->add($child)
            ->append($this->children->drop($position + 1));

        return $document;
    }

    public function prependChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = Sequence::of(
            $child,
            ...$this->children->toList(),
        );

        return $document;
    }

    public function appendChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = ($this->children)($child);

        return $document;
    }

    /** @psalm-suppress InvalidNullableReturnType */
    public function encoding(): Encoding
    {
        /** @psalm-suppress NullableReturnStatement */
        return $this->encoding;
    }

    public function encodingIsSpecified(): bool
    {
        return $this->encoding instanceof Encoding;
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
        $string = \sprintf(
            '<?xml version="%s"%s?>',
            $this->version->toString(),
            $this->encoding instanceof Encoding ? ' encoding="'.$this->encoding->toString().'"' : '',
        );

        if ($this->type instanceof Type) {
            $string .= "\n".$this->type->toString();
        }

        return $string."\n".$this->content();
    }
}
