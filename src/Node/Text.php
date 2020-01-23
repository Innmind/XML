<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\MapInterface;

final class Text implements Node
{
    private CharacterData $data;

    public function __construct(string $data)
    {
        $this->data = new CharacterData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function children(): MapInterface
    {
        return $this->data->children();
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
        return $this->data->content();
    }

    public function toString(): string
    {
        return $this->data->content();
    }
}
