<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element,
    Node,
    Attribute,
    Exception\DomainException,
    Exception\LogicException,
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
    private string $name;
    /** @var Map<non-empty-string, Attribute> */
    private Map $attributes;

    /**
     * @param Map<non-empty-string, Attribute> $attributes
     */
    private function __construct(string $name, Map $attributes)
    {
        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     */
    public static function of(string $name, Set $attributes = null): self
    {
        /** @var Set<Attribute> */
        $attributes ??= Set::of();

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

    public function prependChild(Node $child): Node
    {
        throw new LogicException('Operation not applicable');
    }

    public function appendChild(Node $child): Node
    {
        throw new LogicException('Operation not applicable');
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
