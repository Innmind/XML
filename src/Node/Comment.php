<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class Comment implements Implementation
{
    private function __construct(private string $value)
    {
    }

    /**
     * @psalm-pure
     */
    public static function of(string $value): self
    {
        return new self($value);
    }

    #[\Override]
    public function content(): string
    {
        return $this->value;
    }

    #[\Override]
    public function render(\XMLWriter $writer): string
    {
        /** @psalm-suppress ImpureMethodCall */
        $writer->writeComment($this->value);

        /** @psalm-suppress ImpureMethodCall */
        return $writer->outputMemory();
    }
}
