<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    NodeInterface,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Exception\InvalidArgumentException,
    Exception\OutOfBoundsException
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

final class Document implements NodeInterface
{
    private $version;
    private $type;
    private $children;
    private $encoding;

    public function __construct(
        Version $version,
        Type $type = null,
        MapInterface $children = null,
        Encoding $encoding = null
    ) {
        $children = $children ?? new Map('int', NodeInterface::class);

        if (
            (string) $children->keyType() !== 'int' ||
            (string) $children->valueType() !== NodeInterface::class
        ) {
            throw new InvalidArgumentException;
        }

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
    public function children(): MapInterface
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children->size() > 0;
    }

    public function removeChild(int $position): NodeInterface
    {
        if (!$this->children->contains($position)) {
            throw new OutOfBoundsException;
        }

        $document = clone $this;
        $document->children = $this
            ->children
            ->reduce(
                new Map('int', NodeInterface::class),
                function(Map $children, int $pos, NodeInterface $node) use ($position): Map {
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

    public function replaceChild(int $position, NodeInterface $node): NodeInterface
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

    public function prependChild(NodeInterface $child): NodeInterface
    {
        $document = clone $this;
        $document->children = $this
            ->children
            ->reduce(
                (new Map('int', NodeInterface::class))
                    ->put(0, $child),
                function(Map $children, int $position, NodeInterface $child): Map {
                    return $children->put(
                        $children->size(),
                        $child
                    );
                }
            );

        return $document;
    }

    public function appendChild(NodeInterface $child): NodeInterface
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
        return (string) $this->children->join('');
    }

    public function __toString(): string
    {
        $string = sprintf(
            '<?xml version="%s"%s?>',
            (string) $this->version,
            $this->encodingIsSpecified() ? ' encoding="'.$this->encoding.'"' : ''
        );

        if ($this->hasType()) {
            $string .= "\n".(string) $this->type;
        }

        return $string."\n".$this->content();
    }
}
