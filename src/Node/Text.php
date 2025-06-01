<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class Text implements Implementation
{
    private function __construct(private string $data)
    {
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
    public function render(\XMLWriter $writer): string
    {
        /** @psalm-suppress ImpureMethodCall */
        $writer->text($this->data);

        /** @psalm-suppress ImpureMethodCall */
        return $writer->outputMemory();
    }
}
