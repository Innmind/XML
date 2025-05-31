<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
final class Text implements Implementation
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
    public function content(): string
    {
        return $this->data->content();
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
        $writer->text($this->data->content());

        return $writer->outputMemory();
    }
}
