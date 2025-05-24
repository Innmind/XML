<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\{
    Node,
    Element,
};
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

    #[\Override]
    public function children(): Sequence
    {
        return Sequence::of();
    }

    #[\Override]
    public function filterChild(callable $filter): self
    {
        return $this;
    }

    #[\Override]
    public function mapChild(callable $map): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    #[\Override]
    public function prependChild(Node|Element $child): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    #[\Override]
    public function appendChild(Node|Element $child): self
    {
        return $this;
    }

    #[\Override]
    public function content(): string
    {
        return $this->data;
    }

    #[\Override]
    public function toString(): string
    {
        return "&{$this->data};";
    }
}
