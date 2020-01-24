<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Node,
    Attribute,
    Exception\LogicException,
};
use Innmind\Immutable\Set;
use function Innmind\Immutable\join;

class SelfClosingElement extends Element
{
    private ?string $string = null;

    /**
     * @param Set<Attribute>|null $attributes
     */
    public function __construct(string $name, Set $attributes = null)
    {
        parent::__construct($name, $attributes);
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function removeChild(int $position): Node
    {
        throw new LogicException('Operation not applicable');
    }

    public function replaceChild(int $position, Node $node): Node
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
        if ($this->string === null) {
            $attributes = $this
                ->attributes()
                ->values()
                ->mapTo(
                    'string',
                    static fn(Attribute $attribute): string => $attribute->toString(),
                );

            $this->string = sprintf(
                '<%s%s/>',
                $this->name(),
                !$this->attributes()->empty() ? ' '.join(' ', $attributes)->toString() : ''
            );
        }

        return $this->string;
    }
}
