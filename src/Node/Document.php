<?php
declare(strict_types = 1);

namespace Innmind\XML\Node;

use Innmind\XML\{
    NodeInterface,
    Node\Document\Type,
    Node\Document\Version,
    Node\Document\Encoding,
    Exception\InvalidArgumentException
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
            $this->encodingIsSpecified() ? ' '.$this->encoding : ''
        );

        if ($this->hasType()) {
            $string .= (string) $this->type;
        }

        return $string.$this->content();
    }
}
