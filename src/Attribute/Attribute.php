<?php
declare(strict_types = 1);

namespace Innmind\Xml\Attribute;

use Innmind\Xml\{
    Attribute as AttributeInterface,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

class Attribute implements AttributeInterface
{
    private string $name;
    private string $value;
    private ?string $string = null;

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
        if ($this->string === null) {
            $this->string = $this->name;

            if (!Str::of($this->value)->empty()) {
                $this->string .= sprintf(
                    '="%s"',
                    $this->value
                );
            }
        }

        return $this->string;
    }
}
