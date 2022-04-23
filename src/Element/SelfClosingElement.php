<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Node,
    Attribute,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Set,
    Str,
};

/**
 * @psalm-immutable
 */
class SelfClosingElement extends Element
{
    /**
     * @param Set<Attribute>|null $attributes
     */
    public function __construct(string $name, Set $attributes = null)
    {
        parent::__construct($name, $attributes);
    }

    public function filterChild(callable $filter): self
    {
        return $this;
    }

    public function mapChild(callable $map): self
    {
        return $this;
    }

    public function replaceChild(int $position, Node $child): Node
    {
        throw new LogicException('Operation not applicable');
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
