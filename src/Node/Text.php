<?php
declare(strict_types = 1);

namespace Innmind\XML\Node;

use Innmind\XML\NodeInterface;
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

    public function content(): string
    {
        return $this->data->content();
    }

    public function __toString(): string
    {
        return $this->data->content();
    }
}
