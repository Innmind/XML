<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Element\Name;
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Map,
    Maybe,
    Sequence,
    Set,
    Str,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class Element implements AsContent
{
    /**
     * @param Map<non-empty-string, Attribute> $attributes
     * @param Sequence<Node|self> $children
     */
    private function __construct(
        private Name $name,
        private Map $attributes,
        private Sequence $children,
        private bool $selfClosing,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     * @param Sequence<Node|self>|null $children
     */
    public static function of(
        Name $name,
        ?Set $attributes = null,
        ?Sequence $children = null,
    ): self {
        $attributes ??= Set::of()->keep(Instance::of(Attribute::class));
        /** @var Sequence<Node|self> */
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
            false,
        );
    }

    /**
     * @psalm-pure
     *
     * @param Set<Attribute>|null $attributes
     */
    public static function selfClosing(
        Name $name,
        ?Set $attributes = null,
    ): self {
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
            Sequence::of(),
            true,
        );
    }

    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return Map<non-empty-string, Attribute>
     */
    public function attributes(): Map
    {
        return $this->attributes;
    }

    /**
     * @param non-empty-string $name
     *
     * @return Maybe<Attribute>
     */
    public function attribute(string $name): Maybe
    {
        return $this->attributes->get($name);
    }

    /**
     * @param non-empty-string $name
     */
    public function removeAttribute(string $name): self
    {
        if (!$this->attributes->contains($name)) {
            return $this;
        }

        return new self(
            $this->name,
            $this->attributes->remove($name),
            $this->children,
            $this->selfClosing,
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
            $this->children,
            $this->selfClosing,
        );
    }

    /**
     * @return Sequence<Node|self>
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    /**
     * @param callable(Node|self): bool $filter
     */
    public function filterChild(callable $filter): self
    {
        if ($this->selfClosing) {
            return $this;
        }

        return new self(
            $this->name,
            $this->attributes,
            $this->children->filter($filter),
            $this->selfClosing,
        );
    }

    /**
     * @param callable(Node|self): Node $map
     */
    public function mapChild(callable $map): self
    {
        if ($this->selfClosing) {
            return $this;
        }

        return new self(
            $this->name,
            $this->attributes,
            $this->children->map($map),
            $this->selfClosing,
        );
    }

    public function prependChild(Node|self $child): self
    {
        if ($this->selfClosing) {
            return $this;
        }

        return new self(
            $this->name,
            $this->attributes,
            $this->children->prepend(Sequence::of($child)),
            $this->selfClosing,
        );
    }

    public function appendChild(Node|self $child): self
    {
        if ($this->selfClosing) {
            return $this;
        }

        return new self(
            $this->name,
            $this->attributes,
            ($this->children)($child),
            $this->selfClosing,
        );
    }

    public function content(): string
    {
        $children = $this->children->map(
            static fn($node) => $node->toString(),
        );

        return Str::of('')->join($children)->toString();
    }

    public function toString(): string
    {
        if ($this->selfClosing) {
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

        return \sprintf(
            '%s%s%s',
            $this->openingTag(),
            $this->content(),
            $this->closingTag(),
        );
    }

    #[\Override]
    public function asContent(): Content
    {
        if ($this->selfClosing) {
            return Content::ofString($this->toString());
        }

        return Content::ofLines(
            $this
                ->children
                ->flatMap(
                    static fn($node) => match (true) {
                        $node instanceof AsContent => $node->asContent()->lines(),
                        default => Content::ofString($node->toString())->lines(),
                    },
                )
                ->map(static fn($line) => $line->map(
                    static fn($string) => $string->prepend('    '), // to correctly indent the file
                ))
                ->prepend(Sequence::of(Content\Line::of(Str::of($this->openingTag()))))
                ->add(Content\Line::of(Str::of($this->closingTag()))),
        );
    }

    private function openingTag(): string
    {
        $attributes = $this
            ->attributes
            ->values()
            ->map(
                static fn(Attribute $attribute): string => $attribute->toString(),
            );

        return \sprintf(
            '<%s%s>',
            $this->name()->toString(),
            !$attributes->empty() ? ' '.Str::of(' ')->join($attributes)->toString() : '',
        );
    }

    private function closingTag(): string
    {
        return \sprintf(
            '</%s>',
            $this->name()->toString(),
        );
    }
}
