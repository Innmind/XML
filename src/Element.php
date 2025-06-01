<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Element\{
    Name,
    Custom,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Maybe,
    Sequence,
    Set,
    Str,
};

/**
 * @psalm-immutable
 */
final class Element
{
    /**
     * @param Sequence<Attribute> $attributes
     * @param Sequence<Node|self|Custom> $children
     */
    private function __construct(
        private Name $name,
        private Sequence $attributes,
        private Sequence $children,
        private bool $selfClosing,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param Sequence<Attribute>|null $attributes
     * @param Sequence<Node|self|Custom>|null $children
     */
    public static function of(
        Name $name,
        ?Sequence $attributes = null,
        ?Sequence $children = null,
    ): self {
        /** @var Sequence<Attribute> */
        $attributes ??= Sequence::of();
        /** @var Sequence<Node|self|Custom> */
        $children ??= Sequence::of();

        return new self(
            $name,
            self::safeguard($attributes),
            $children,
            false,
        );
    }

    /**
     * @psalm-pure
     *
     * @param Sequence<Attribute>|null $attributes
     */
    public static function selfClosing(
        Name $name,
        ?Sequence $attributes = null,
    ): self {
        $attributes ??= Sequence::of();

        return new self(
            $name,
            self::safeguard($attributes),
            Sequence::of(),
            true,
        );
    }

    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return Sequence<Attribute>
     */
    public function attributes(): Sequence
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
        return $this->attributes->find(
            static fn($attribute) => $attribute->name() === $name,
        );
    }

    /**
     * @param non-empty-string $name
     */
    public function removeAttribute(string $name): self
    {
        return new self(
            $this->name,
            $this->attributes->exclude(
                static fn($attribute) => $attribute->name() === $name,
            ),
            $this->children,
            $this->selfClosing,
        );
    }

    public function addAttribute(Attribute $attribute): self
    {
        return new self(
            $this->name,
            $this
                ->attributes
                ->exclude(static fn($existing) => $existing->name() === $attribute->name())
                ->add($attribute),
            $this->children,
            $this->selfClosing,
        );
    }

    /**
     * @return Sequence<Node|self|Custom>
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    /**
     * @param callable(Node|self|Custom): bool $filter
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
     * @param callable(Node|self|Custom): (Node|self|Custom) $map
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

    public function prependChild(Node|self|Custom $child): self
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

    public function appendChild(Node|self|Custom $child): self
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

    public function asContent(Format $format = Format::pretty): Content
    {
        $writer = new \XMLWriter;
        /** @psalm-suppress ImpureMethodCall */
        $writer->openMemory();

        if ($format === Format::pretty) {
            /** @psalm-suppress ImpureMethodCall */
            $writer->setIndent(true);
            /** @psalm-suppress ImpureMethodCall */
            $writer->setIndentString('    ');
        }

        return Content::ofChunks(
            $this->render($writer),
        );
    }

    /**
     * @return Sequence<Str>
     */
    private function render(\XMLWriter $writer): Sequence
    {
        $selfClosing = $this->selfClosing;

        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress PossiblyNullFunctionCall
         */
        (\Closure::bind(
            fn() => $this->render($writer),
            $this->name,
            $this->name::class,
        ))();
        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress PossiblyNullFunctionCall
         */
        $_ = $this->attributes->foreach(
            static fn($attribute): mixed => (\Closure::bind(
                fn() => $this->render($writer),
                $attribute,
                $attribute::class,
            ))(),
        );

        /** @psalm-suppress ImpureMethodCall */
        $opening = Sequence::of(Str::of($writer->outputMemory()));
        $closing = Sequence::lazy(static function() use ($writer, $selfClosing) {
            /** @psalm-suppress ImpureMethodCall */
            match ($selfClosing) {
                true => $writer->endElement(),
                false => $writer->fullEndElement(),
            };

            /** @psalm-suppress ImpureMethodCall */
            yield Str::of($writer->outputMemory());
        });
        $children = $this->children->flatMap(
            static function($child) use ($writer) {
                if ($child instanceof Node) {
                    $write = \Closure::bind(
                        fn() => $this->render($writer),
                        $child,
                        Node::class,
                    );

                    /**
                     * @psalm-suppress PossiblyNullFunctionCall
                     * @var Sequence<Str>
                     */
                    return $write();
                }

                if ($child instanceof Custom) {
                    $child = $child->normalize();
                }

                return $child->render($writer);
            },
        );

        return $children
            ->prepend($opening)
            ->append($closing);
    }

    /**
     * @psalm-pure
     *
     * @param Sequence<Attribute> $attributes
     *
     * @return Sequence<Attribute>
     */
    private static function safeguard(Sequence $attributes): Sequence
    {
        return $attributes->safeguard(
            Set::strings(),
            static fn($names, $attribute) => match ($names->contains($attribute->name())) {
                true => throw new \LogicException(\sprintf(
                    'Duplicated attribute %s',
                    $attribute->name(),
                )),
                false => ($names)($attribute->name()),
            },
        );
    }
}
