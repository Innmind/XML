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
use Innmind\Immutable\Map;
use function Innmind\Immutable\{
    assertMap,
    join,
};

final class Document implements Node
{
    private Version $version;
    private ?Type $type = null;
    private Map $children;
    private ?Encoding $encoding = null;

    public function __construct(
        Version $version,
        Type $type = null,
        Map $children = null,
        Encoding $encoding = null
    ) {
        $children ??= Map::of('int', Node::class);

        assertMap('int', Node::class, $children, 3);

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
    public function children(): Map
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children->size() > 0;
    }

    public function removeChild(int $position): Node
    {
        if (!$this->children->contains($position)) {
            throw new OutOfBoundsException;
        }

        $document = clone $this;
        $document->children = $this
            ->children
            ->reduce(
                Map::of('int', Node::class),
                function(Map $children, int $pos, Node $node) use ($position): Map {
                    if ($pos === $position) {
                        return $children;
                    }

                    return $children->put(
                        $children->size(),
                        $node
                    );
                }
            );

        return $document;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        if (!$this->children->contains($position)) {
            throw new OutOfBoundsException;
        }

        $document = clone $this;
        $document->children = $this->children->put(
            $position,
            $node
        );

        return $document;
    }

    public function prependChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = $this
            ->children
            ->reduce(
                Map::of('int', Node::class)
                    (0, $child),
                function(Map $children, int $position, Node $child): Map {
                    return $children->put(
                        $children->size(),
                        $child
                    );
                }
            );

        return $document;
    }

    public function appendChild(Node $child): Node
    {
        $document = clone $this;
        $document->children = $this->children->put(
            $this->children->size(),
            $child
        );

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
        $children = $this
            ->children
            ->values()
            ->mapTo(
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
