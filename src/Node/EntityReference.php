<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 */
final class EntityReference implements Node
{
    private string $data;

    private function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @psalm-pure
     */
    public static function of(string $data): self
    {
        return new self($data);
    }

    public function children(): Sequence
    {
        return Sequence::of();
    }

    public function filterChild(callable $filter): self
    {
        return $this;
    }

    public function mapChild(callable $map): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function prependChild(Node $child): Node
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function appendChild(Node $child): Node
    {
        return $this;
    }

    public function content(): string
    {
        return $this->data;
    }

    public function toString(): string
    {
        return "&{$this->data};";
    }
}
