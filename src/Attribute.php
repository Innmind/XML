<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Str;

/**
 * @psalm-immutable
 */
class Attribute
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value = '')
    {
        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

        $this->name = $name;
        $this->value = $value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        $string = $this->name;

        if (!Str::of($this->value)->empty()) {
            $string .= \sprintf(
                '="%s"',
                $this->value,
            );
        }

        return $string;
    }
}
