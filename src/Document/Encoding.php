<?php
declare(strict_types = 1);

namespace Innmind\Xml\Document;

use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
enum Encoding
{
    case utf8;
    case ascii;

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function of(string $value): Maybe
    {
        return Maybe::of(match ($value) {
            'utf-8', 'UTF-8' => self::utf8,
            'ascii', 'us-ascii', 'ASCII', 'US-ASCII' => self::ascii,
            default => null,
        });
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        // @see https://www.iana.org/assignments/character-sets/character-sets.xml
        // As described in the RFC above, "us-ascii" is the encouraged notation
        return match ($this) {
            self::utf8 => 'utf-8',
            self::ascii => 'us-ascii',
        };
    }
}
