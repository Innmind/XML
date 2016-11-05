<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    NodeInterface,
    Exception\LogicException
};
use Innmind\Immutable\MapInterface;

final class Text implements NodeInterface
{
    private $data;

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
        return $this->data->content();
    }

    public function __toString(): string
    {
        return $this->data->content();
    }
}
