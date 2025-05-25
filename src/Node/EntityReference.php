<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class EntityReference implements Implementation
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
