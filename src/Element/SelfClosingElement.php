<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element,
    Node,
    Attribute,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Set,
    Str,
    Map,
    Sequence,
    Maybe,
};

/**
 * @psalm-immutable
 */
class SelfClosingElement implements Element
{
    /** @var non-empty-string */
    private string $name;
    /** @var Map<non-empty-string, Attribute> */
    private Map $attributes;

    /**
     * @param non-empty-string $name
     * @param Map<non-empty-string, Attribute> $attributes
     */
    private function __construct(string $name, Map $attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     * @param Set<Attribute>|null $attributes
     *
     * @throws DomainException If the name is empty
     */
    public static function of(string $name, Set $attributes = null): self
    {
        return self::maybe($name, $attributes)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException,
        );
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     *
     * @return Maybe<self>
     */
    public static function maybe(string $name, Set $attributes = null): Maybe
    {
        if ($name === '') {
            /** @var Maybe<self> */
            return Maybe::nothing();
        }

        /** @var Set<Attribute> */
        $attributes ??= Set::of();

        return Maybe::just(new self(
            $name,
            Map::of(
                ...$attributes
                    ->map(static fn($attribute) => [
                        $attribute->name(),
                        $attribute,
                    ])
                    ->toList(),
            ),
        ));
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

    public function removeAttribute(string $name): self
    {
        return new self(
            $this->name,
            $this->attributes->remove($name),
        );
    }

    public function addAttribute(Attribute $attribute): self
    {
        return new self(
            $this->name,
            ($this->attributes)(
                $attribute->name(),
                $attribute,
            ),
        );
    }

    public function children(): Sequence
    {
        return Sequence::of();
    }

    public function filterChild(callable $filter): self
    {
        return $this;
    }

    public function mapChild(callable $map): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function prependChild(Node $child): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function appendChild(Node $child): self
    {
        return $this;
    }

    public function content(): string
    {
        return '';
    }

    public function toString(): string
    {
        $attributes = $this
            ->attributes()
            ->values()
            ->map(
                static fn(Attribute $attribute): string => $attribute->toString(),
            );

        return \sprintf(
            '<%s%s/>',
            $this->name(),
            !$this->attributes()->empty() ? ' '.Str::of(' ')->join($attributes)->toString() : '',
        );
    }
}
