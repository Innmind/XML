<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element,
    Node,
    Attribute,
};
use Innmind\Immutable\{
    Set,
    Str,
    Map,
    Sequence,
    Maybe,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class SelfClosingElement implements Element
{
    private Name $name;
    /** @var Map<non-empty-string, Attribute> */
    private Map $attributes;

    /**
     * @param Map<non-empty-string, Attribute> $attributes
     */
    private function __construct(Name $name, Map $attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     */
    public static function of(Name $name, ?Set $attributes = null): self
    {
        $attributes ??= Set::of()->keep(Instance::of(Attribute::class));

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
        );
    }

    #[\Override]
    public function name(): Name
    {
        return $this->name;
    }

    #[\Override]
    public function attributes(): Map
    {
        return $this->attributes;
    }

    #[\Override]
    public function attribute(string $name): Maybe
    {
        return $this->attributes->get($name);
    }

    #[\Override]
    public function removeAttribute(string $name): self
    {
        return new self(
            $this->name,
            $this->attributes->remove($name),
        );
    }

    #[\Override]
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

    #[\Override]
    public function children(): Sequence
    {
        return Sequence::of();
    }

    #[\Override]
    public function filterChild(callable $filter): self
    {
        return $this;
    }

    #[\Override]
    public function mapChild(callable $map): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    #[\Override]
    public function prependChild(Node $child): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    #[\Override]
    public function appendChild(Node $child): self
    {
        return $this;
    }

    #[\Override]
    public function content(): string
    {
        return '';
    }

    #[\Override]
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
            $this->name()->toString(),
            !$this->attributes()->empty() ? ' '.Str::of(' ')->join($attributes)->toString() : '',
        );
    }
}
