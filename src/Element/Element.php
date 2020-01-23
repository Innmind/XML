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
    Map,
    Str,
};
use function Innmind\Immutable\{
    assertMap,
    join,
};

class Element implements ElementInterface
{
    private string $name;
    private Map $attributes;
    private Map $children;
    private ?string $content = null;
    private ?string $string = null;

    public function __construct(
        string $name,
        Map $attributes = null,
        Map $children = null
    ) {
        $attributes ??= Map::of('string', Attribute::class);
        $children ??= Map::of('int', Node::class);

        assertMap('string', Attribute::class, $attributes, 2);
        assertMap('int', Node::class, $children, 3);

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
    public function attributes(): Map
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
        $element->attributes = ($this->attributes)(
            $attribute->name(),
            $attribute,
        );

        return $element;
    }

    public function addAttribute(Attribute $attribute): ElementInterface
    {
        if ($this->attributes->contains($attribute->name())) {
            throw new LogicException;
        }

        $element = clone $this;
        $element->attributes = ($this->attributes)(
            $attribute->name(),
            $attribute,
        );

        return $element;
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

        $element = clone $this;
        $element->children = $this
            ->children
            ->reduce(
                Map::of('int', Node::class),
                function(Map $children, int $pos, Node $node) use ($position): Map {
                    if ($pos === $position) {
                        return $children;
                    }

                    return ($children)(
                        $children->size(),
                        $node,
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
        $element->children = ($this->children)(
            $position,
            $node,
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
                    return ($children)(
                        $children->size(),
                        $child,
                    );
                }
            );

        return $element;
    }

    public function appendChild(Node $child): Node
    {
        $element = clone $this;
        $element->children = ($this->children)(
            $this->children->size(),
            $child,
        );

        return $element;
    }

    public function content(): string
    {
        if ($this->content === null) {
            $children = $this
                ->children
                ->values()
                ->mapTo(
                    'string',
                    static fn(Node $node): string => $node->toString(),
                );

            $this->content = join('', $children)->toString();
        }

        return $this->content;
    }

    public function toString(): string
    {
        if ($this->string === null) {
            $attributes = $this
                ->attributes
                ->values()
                ->mapTo(
                    'string',
                    static fn(Attribute $attribute): string => $attribute->toString(),
                );

            $this->string = sprintf(
                '<%s%s>%s</%s>',
                $this->name(),
                $this->hasAttributes() ? ' '.join(' ', $attributes)->toString() : '',
                $this->content(),
                $this->name()
            );
        }

        return $this->string;
    }
}
