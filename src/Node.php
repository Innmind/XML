<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Node\{
    Implementation,
    CharacterData,
    Comment,
    EntityReference,
    ProcessingInstruction,
    Text,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Sequence,
    Str,
};

/**
 * @psalm-immutable
 */
final class Node
{
    private function __construct(
        private Implementation $implementation,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function characterData(string $data): self
    {
        return new self(CharacterData::of($data));
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function text(string $data): self
    {
        return new self(Text::of($data));
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function comment(string $data): self
    {
        return new self(Comment::of($data));
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function entityReference(string $data): self
    {
        return new self(EntityReference::of($data));
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function processingInstruction(string $kind, string $value): self
    {
        return new self(ProcessingInstruction::of($kind, $value));
    }

    #[\NoDiscard]
    public function content(): string
    {
        return $this->implementation->content();
    }

    #[\NoDiscard]
    public function asContent(): Content
    {
        $writer = new \XMLWriter;
        /** @psalm-suppress ImpureMethodCall */
        $writer->openMemory();

        return Content::ofChunks($this->render($writer));
    }

    /**
     * @return Sequence<Str>
     */
    private function render(\XMLWriter $writer): Sequence
    {
        return Sequence::of(Str::of(
            $this->implementation->render($writer),
        ));
    }
}
