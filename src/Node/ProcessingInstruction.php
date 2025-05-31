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
    private string $kind;
    private string $value;

    private function __construct(string $kind, string $value)
    {
        $this->kind = $kind;
        $this->value = $value;
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
    public function toString(): string
    {
        $writer = new \XMLWriter;
        $writer->openMemory();

        return $this->render($writer);
    }

    #[\Override]
    public function render(\XMLWriter $writer): string
    {
        $writer->writePi($this->kind, $this->value);

        return $writer->outputMemory();
    }
}
