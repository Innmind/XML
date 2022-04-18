<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Str;

/**
 * @psalm-immutable
 */
final class Encoding
{
    private string $string;

    public function __construct(string $string)
    {
        if (!Str::of($string)->matches('~^[a-zA-Z0-9\-_:\(\)]+$~')) {
            throw new DomainException($string);
        }

        $this->string = $string;
    }

    public function toString(): string
    {
        return $this->string;
    }
}
