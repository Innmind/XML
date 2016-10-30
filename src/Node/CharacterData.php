<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\NodeInterface;
use Innmind\Immutable\{
    Map,
    MapInterface
};

final class CharacterData implements NodeInterface
{
    private $value;
    private $children;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->children = new Map('int', NodeInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function children(): MapInterface
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function content(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return '<![CDATA['.$this->value.']]>';
    }
}
