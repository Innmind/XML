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
use Innmind\Immutable\Sequence;
use function Innmind\Immutable\{
    assertSequence,
    join,
    unwrap,
};

final class Document implements Node
{
    private Version $version;
    private ?Type $type = null;
    private Sequence $children;
    private ?Encoding $encoding = null;

    public function __construct(
        Version $version,
        Type $type = null,
        Sequence $children = null,
        Encoding $encoding = null
    ) {
        $children ??= Sequence::of(Node::class);

        assertSequence(Node::class, $children, 3);

        $this->version = $version;
        $this->type = $type;
        $this->children = $children;
        $this->encoding = $encoding;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function hasType(): bool
    {
        return $this->type instanceof Type;
    }

    /**
     * {@inheritdoc}
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children->size() > 0;
    }

    public function removeChild(int $position): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException;
        }

        $document = clone $this;
        $document->children = $this
            ->children
            ->take($position)
            ->append($this->children->drop($position + 1));

        return $document;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException;
        }

        $document = clone $this;
        $document->children = $this
            ->children
            ->take($position)
            ->add($node)
            ->append($this->children->drop($position + 1));

        return $document;
    }

    public function prependChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = Sequence::of(
            Node::class,
            $child,
            ...unwrap($this->children),
        );

        return $document;
    }

    public function appendChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = ($this->children)($child);

        return $document;
    }

    public function encoding(): Encoding
    {
        return $this->encoding;
    }

    public function encodingIsSpecified(): bool
    {
        return $this->encoding instanceof Encoding;
    }

    public function content(): string
    {
        $children = $this->children->mapTo(
            'string',
            static fn(Node $child): string => $child->toString(),
        );

        return join('', $children)->toString();
    }

    public function toString(): string
    {
        $string = sprintf(
            '<?xml version="%s"%s?>',
            $this->version->toString(),
            $this->encodingIsSpecified() ? ' encoding="'.$this->encoding->toString().'"' : ''
        );

        if ($this->hasType()) {
            $string .= "\n".$this->type->toString();
        }

        return $string."\n".$this->content();
    }
}
