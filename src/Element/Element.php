<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element as ElementInterface,
    Attribute,
    Node,
    Exception\InvalidArgumentException,
    Exception\OutOfBoundsException,
    Exception\LogicException,
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
        $attributes = $attributes ?? new Map('string', Attribute::class);
        $children = $children ?? new Map('int', Node::class);

        if (
            empty($name) ||
            (string) $attributes->keyType() !== 'string' ||
            (string) $attributes->valueType() !== Attribute::class ||
            (string) $children->keyType() !== 'int' ||
            (string) $children->valueType() !== Node::class
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

    public function attribute(string $name): Attribute
    {
        return $this->attributes->get($name);
    }

    public function removeAttribute(string $name): ElementInterface
    {
        if (!$this->attributes->contains($name)) {
            throw new OutOfBoundsException;
        }

        $element = clone $this;
        $element->attributes = $this->attributes->remove($name);

        return $element;
    }

    public function replaceAttribute(Attribute $attribute): ElementInterface
    {
        if (!$this->attributes->contains($attribute->name())) {
            throw new OutOfBoundsException;
        }

        $element = clone $this;
        $element->attributes = $this->attributes->put(
            $attribute->name(),
            $attribute
        );

        return $element;
    }

    public function addAttribute(Attribute $attribute): ElementInterface
    {
        if ($this->attributes->contains($attribute->name())) {
            throw new LogicException;
        }

        $element = clone $this;
        $element->attributes = $this->attributes->put(
            $attribute->name(),
            $attribute
        );

        return $element;
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

    public function removeChild(int $position): Node
    {
        if (!$this->children->contains($position)) {
            throw new OutOfBoundsException;
        }

        $element = clone $this;
        $element->children = $this
            ->children
            ->reduce(
                new Map('int', Node::class),
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

        return $element;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        if (!$this->children->contains($position)) {
            throw new OutOfBoundsException;
        }

        $element = clone $this;
        $element->children = $this->children->put(
            $position,
            $node
        );

        return $element;
    }

    public function prependChild(Node $child): Node
    {
        $element = clone $this;
        $element->children = $this
            ->children
            ->reduce(
                (new Map('int', Node::class))
                    ->put(0, $child),
                function(Map $children, int $position, Node $child): Map {
                    return $children->put(
                        $children->size(),
                        $child
                    );
                }
            );

        return $element;
    }

    public function appendChild(Node $child): Node
    {
        $element = clone $this;
        $element->children = $this->children->put(
            $this->children->size(),
            $child
        );

        return $element;
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
