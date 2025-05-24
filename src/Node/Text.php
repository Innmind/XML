<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 */
final class Text implements Node
{
    private CharacterData $data;

    private function __construct(string $data)
    {
        $this->data = CharacterData::of($data);
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
        return $this->data->children();
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
    public function prependChild(Node $child): Node
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    #[\Override]
    public function appendChild(Node $child): Node
    {
        return $this;
    }

    #[\Override]
    public function content(): string
    {
        return $this->data->content();
    }

    #[\Override]
    public function toString(): string
    {
        return $this->data->content();
    }
}
