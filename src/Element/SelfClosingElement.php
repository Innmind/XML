<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Node,
    Attribute,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

class SelfClosingElement extends Element
{
    private ?string $string = null;

    public function __construct(
        string $name,
        MapInterface $attributes = null
    ) {
        parent::__construct(
            $name,
            $attributes,
            new Map('int', Node::class)
        );
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function removeChild(int $position): Node
    {
        throw new LogicException;
    }

    public function replaceChild(int $position, Node $node): Node
    {
        throw new LogicException;
    }

    public function prependChild(Node $child): Node
    {
        throw new LogicException;
    }

    public function appendChild(Node $child): Node
    {
        throw new LogicException;
    }

    public function content(): string
    {
        return '';
    }

    public function toString(): string
    {
        if ($this->string === null) {
            $attributes = $this->attributes()->reduce(
                [],
                static function(array $attributes, string $name, Attribute $attribute): array {
                    $attributes[] = $attribute->toString();

                    return $attributes;
                },
            );

            $this->string = sprintf(
                '<%s%s/>',
                $this->name(),
                $this->hasAttributes() ? ' '.\implode(' ', $attributes) : ''
            );
        }

        return $this->string;
    }
}
