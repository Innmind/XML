<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element as ElementInterface,
    Attribute,
    Node,
    Exception\DomainException,
    Exception\OutOfBoundsException,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
    Str,
};

class Element implements ElementInterface
{
    private string $name;
    private MapInterface $attributes;
    private MapInterface $children;
    private ?string $content = null;
    private ?string $string = null;

    public function __construct(
        string $name,
        MapInterface $attributes = null,
        MapInterface $children = null
    ) {
        $attributes ??= new Map('string', Attribute::class);
        $children ??= new Map('int', Node::class);

        if (
            (string) $attributes->keyType() !== 'string' ||
            (string) $attributes->valueType() !== Attribute::class
        ) {
            throw new \TypeError(sprintf(
                'Argument 2 must be of type MapInterface<string, %s>',
                Attribute::class
            ));
        }

        if (
            (string) $children->keyType() !== 'int' ||
            (string) $children->valueType() !== Node::class
        ) {
            throw new \TypeError(sprintf(
                'Argument 3 must be of type MapInterface<int, %s>',
                Node::class
            ));
        }

        if (Str::of($name)->empty()) {
            throw new DomainException;
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
                Map::of('int', Node::class)
                    (0, $child),
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
            $children = $this->children->reduce(
                [],
                static function(array $children, int $index, Node $child): array {
                    $children[] = $child->toString();

                    return $children;
                },
            );

            $this->content = \implode('', $children);
        }

        return $this->content;
    }

    public function toString(): string
    {
        if ($this->string === null) {
            $attributes = $this->attributes->reduce(
                [],
                static function(array $attributes, string $name, Attribute $attribute): array {
                    $attributes[] = $attribute->toString();

                    return $attributes;
                },
            );

            $this->string = sprintf(
                '<%s%s>%s</%s>',
                $this->name(),
                $this->hasAttributes() ? ' '.\implode(' ', $attributes) : '',
                $this->content(),
                $this->name()
            );
        }

        return $this->string;
    }
}
