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
    Sequence,
    Set,
    Str,
};
use function Innmind\Immutable\{
    assertSet,
    join,
    unwrap,
};

class Element implements ElementInterface
{
    private string $name;
    /** @var Map<string, Attribute> */
    private Map $attributes;
    /** @var Sequence<Node> */
    private Sequence $children;
    private ?string $content = null;
    private ?string $string = null;

    /**
     * @param Set<Attribute>|null $attributes
     */
    public function __construct(
        string $name,
        Set $attributes = null,
        Node ...$children
    ) {
        /** @var Set<Attribute> */
        $attributes ??= Set::of(Attribute::class);

        assertSet(Attribute::class, $attributes, 2);

        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->name = $name;
        /** @var Map<string, Attribute> */
        $this->attributes = $attributes->toMapOf(
            'string',
            Attribute::class,
            static function(Attribute $attribute): \Generator {
                yield $attribute->name() => $attribute;
            },
        );
        /** @var Sequence<Node> */
        $this->children = Sequence::of(Node::class, ...$children);
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

    /**
     * {@inheritdoc}
     */
    public function children(): Sequence
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children->size() > 0;
    }

    public function removeChild(int $position): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException((string) $position);
        }

        $element = clone $this;
        $element->children = $this
            ->children
            ->take($position)
            ->append($this->children->drop($position + 1));

        return $element;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        if (!$this->children->indices()->contains($position)) {
            throw new OutOfBoundsException((string) $position);
        }

        $element = clone $this;
        $element->children = $this
            ->children
            ->take($position)
            ->add($node)
            ->append($this->children->drop($position + 1));

        return $element;
    }

    public function prependChild(Node $child): Node
    {
        $element = clone $this;
        /** @var Sequence<Node> */
        $element->children = Sequence::of(
            Node::class,
            $child,
            ...unwrap($this->children),
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
        if ($this->content === null) {
            $children = $this->children->mapTo(
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
