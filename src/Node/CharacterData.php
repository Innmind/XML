<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class CharacterData implements Implementation
{
    private function __construct(private string $value)
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
        return $this->value;
    }

    #[\Override]
    public function render(\XMLWriter $writer): string
    {
        /** @psalm-suppress ImpureMethodCall */
        $writer->writeCdata($this->value);

        /** @psalm-suppress ImpureMethodCall */
        return $writer->outputMemory();
    }
}
