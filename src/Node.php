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
    public static function characterData(string $data): self
    {
        return new self(CharacterData::of($data));
    }

    /**
     * @psalm-pure
     */
    public static function text(string $data): self
    {
        return new self(Text::of($data));
    }

    /**
     * @psalm-pure
     */
    public static function comment(string $data): self
    {
        return new self(Comment::of($data));
    }

    /**
     * @psalm-pure
     */
    public static function entityReference(string $data): self
    {
        return new self(EntityReference::of($data));
    }

    /**
     * @psalm-pure
     */
    public static function processingInstruction(string $kind, string $value): self
    {
        return new self(ProcessingInstruction::of($kind, $value));
    }

    public function content(): string
    {
        return $this->implementation->content();
    }

    public function asContent(): Content
    {
        return Content::ofString($this->implementation->content());
    }

    public function toString(): string
    {
        return $this->implementation->toString();
    }
}
