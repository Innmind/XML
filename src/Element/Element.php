<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element as ElementInterface,
    Attribute,
    Node,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Map,
    Sequence,
    Set,
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
class Element implements ElementInterface
{
    private string $name;
    /** @var Map<non-empty-string, Attribute> */
    private Map $attributes;
    /** @var Sequence<Node> */
    private Sequence $children;

    /**
     * @param Map<non-empty-string, Attribute> $attributes
     * @param Sequence<Node> $children
     */
    private function __construct(
        string $name,
        Map $attributes,
        Sequence $children,
    ) {
        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     * @param Sequence<Node>|null $children
     */
    public static function of(
        string $name,
        Set $attributes = null,
        Sequence $children = null,
    ): self {
        /** @var Set<Attribute> */
        $attributes ??= Set::of();
        /** @var Sequence<Node> */
        $children ??= Sequence::of();

        return new self(
            $name,
            Map::of(
                ...$attributes
                    ->map(static fn($attribute) => [
                        $attribute->name(),
                        $attribute,
                    ])
                    ->toList(),
            ),
            $children,
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function attributes(): Map
    {
        return $this->attributes;
    }

    public function attribute(string $name): Maybe
    {
        return $this->attributes->get($name);
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

    public function filterChild(callable $filter): self
    {
        return new self(
            $this->name,
            $this->attributes,
            $this->children->filter($filter),
        );
    }

    public function mapChild(callable $map): self
    {
        return new self(
            $this->name,
            $this->attributes,
            $this->children->map($map),
        );
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
