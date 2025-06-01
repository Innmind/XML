<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 * @see http://xmlplease.com/xml/pi/
 */
final class ProcessingInstruction implements Implementation
{
    private function __construct(
        private string $kind,
        private string $value,
    ) {
    }

    /**
     * @psalm-pure
     */
    public static function of(string $kind, string $value): self
    {
        return new self($kind, $value);
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
        $writer->writePi($this->kind, $this->value);

        /** @psalm-suppress ImpureMethodCall */
        return $writer->outputMemory();
    }
}
