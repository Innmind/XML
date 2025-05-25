<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class CharacterData implements Implementation
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
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
    public function toString(): string
    {
        $writer = new \XMLWriter;
        $writer->openMemory();
        $writer->writeCdata($this->value);

        return $writer->outputMemory();
    }
}
