<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    NodeInterface,
    Exception\LogicException,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

class SelfClosingElement extends Element
{
    private $string;

    public function __construct(
        string $name,
        MapInterface $attributes = null
    ) {
        parent::__construct(
            $name,
            $attributes,
            new Map('int', NodeInterface::class)
        );
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function removeChild(int $position): NodeInterface
    {
        throw new LogicException;
    }

    public function replaceChild(int $position, NodeInterface $node): NodeInterface
    {
        throw new LogicException;
    }

    public function prependChild(NodeInterface $child): NodeInterface
    {
        throw new LogicException;
    }

    public function appendChild(NodeInterface $child): NodeInterface
    {
        throw new LogicException;
    }

    public function content(): string
    {
        return '';
    }

    public function __toString(): string
    {
        if ($this->string === null) {
            $this->string = sprintf(
                '<%s%s/>',
                $this->name(),
                $this->hasAttributes() ? ' '.$this->attributes()->join(' ') : ''
            );
        }

        return $this->string;
    }
}
