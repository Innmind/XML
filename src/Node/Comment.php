<?php
declare(strict_types = 1);

namespace Innmind\XML\Node;

use Innmind\XML\NodeInterface;
use Innmind\Immutable\{
    Map,
    MapInterface
};

final class Comment implements NodeInterface
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
        return '<!--'.$this->value.'-->';
    }
}
