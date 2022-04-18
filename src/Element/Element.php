<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element as ElementInterface,
    Attribute,
    Node,
    Exception\DomainException,
    Exception\OutOfBoundsException,
};
use Innmind\Immutable\{
    Map,
    Sequence,
    Set,
    Str,
};

/**
 * @psalm-immutable
 */
class Element implements ElementInterface
{
    private string $name;
    /** @var Map<string, Attribute> */
    private Map $attributes;
    /** @var Sequence<Node> */
    private Sequence $children;

    /**
     * @no-named-arguments
     * @param Set<Attribute>|null $attributes
     */
    public function __construct(
        string $name,
        Set $attributes = null,
        Node ...$children,
    ) {
        /** @var Set<Attribute> */
        $attributes ??= Set::of();

        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->name = $name;
        $this->attributes = Map::of(
            ...$attributes
                ->map(static fn($attribute) => [
                    $attribute->name(),
                    $attribute,
                ])
                ->toList(),
        );
        $this->children = Sequence::of(...$children);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function attributes(): Map
    {
        return $this->attributes;
    }

    public function attribute(string $name): Attribute
    {
        return $this->attributes->get($name)->match(
            static fn($attribute) => $attribute,
            static fn() => throw new \LogicException,
        );
    }

    public function removeAttribute(string $name): ElementInterface
    {
        if (!$this->attributes->contains($name)) {
            return $this;
        }

        $element = clone $this;
        $element->attributes = $this->attributes->remove($name);

        return $element;
    }

    public function addAttribute(Attribute $attribute): ElementInterface
    {
        $element = clone $this;
        $element->attributes = ($this->attributes)(
            $attribute->name(),
            $attribute,
        );

        return $element;
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

        $element = clone $this;
        /** @psalm-suppress ArgumentTypeCoercion */
        $element->children = $this
            ->children
            ->take($position)
            ->append($this->children->drop($position + 1));

        return $element;
    }

    public function replaceChild(int $position, Node $child): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException((string) $position);
        }

        $element = clone $this;
        /** @psalm-suppress ArgumentTypeCoercion */
        $element->children = $this
            ->children
            ->take($position)
            ->add($child)
            ->append($this->children->drop($position + 1));

        return $element;
    }

    public function prependChild(Node $child): Node
    {
        $element = clone $this;
        $element->children = Sequence::of(
            $child,
            ...$this->children->toList(),
        );

        return $element;
    }

    public function appendChild(Node $child): Node
    {
        $element = clone $this;
        $element->children = ($this->children)($child);

        return $element;
    }

    public function content(): string
    {
        $children = $this->children->map(
            static fn(Node $node): string => $node->toString(),
        );

        return Str::of('')->join($children)->toString();
    }

    public function toString(): string
    {
        $attributes = $this
            ->attributes
            ->values()
            ->map(
                static fn(Attribute $attribute): string => $attribute->toString(),
            );

        return \sprintf(
            '<%s%s>%s</%s>',
            $this->name(),
            !$this->attributes()->empty() ? ' '.Str::of(' ')->join($attributes)->toString() : '',
            $this->content(),
            $this->name(),
        );
    }
}
