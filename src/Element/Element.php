<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    ElementInterface,
    AttributeInterface,
    NodeInterface,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

class Element implements ElementInterface
{
    private $name;
    private $attributes;
    private $children;
    private $content;
    private $string;

    public function __construct(
        string $name,
        MapInterface $attributes = null,
        MapInterface $children = null
    ) {
        $attributes = $attributes ?? new Map('string', AttributeInterface::class);
        $children = $children ?? new Map('int', NodeInterface::class);

        if (
            empty($name) ||
            (string) $attributes->keyType() !== 'string' ||
            (string) $attributes->valueType() !== AttributeInterface::class ||
            (string) $children->keyType() !== 'int' ||
            (string) $children->valueType() !== NodeInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(): MapInterface
    {
        return $this->attributes;
    }

    public function hasAttributes(): bool
    {
        return $this->attributes->size() > 0;
    }

    public function attribute(string $name): AttributeInterface
    {
        return $this->attributes->get($name);
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

    public function content(): string
    {
        if ($this->content === null) {
            $this->content = (string) $this->children->join('');
        }

        return $this->content;
    }

    public function __toString(): string
    {
        if ($this->string === null) {
            $this->string = sprintf(
                '<%s%s>%s</%s>',
                $this->name(),
                $this->hasAttributes() ? ' '.$this->attributes->join(' ') : '',
                $this->content(),
                $this->name()
            );
        }

        return $this->string;
    }
}
