<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Exception\InvalidArgumentException;

class Attribute implements AttributeInterface
{
    private $name;
    private $value;
    private $string;

    public function __construct(string $name, string $value = '')
    {
        if (empty($name)) {
            throw new InvalidArgumentException;
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

    public function __toString(): string
    {
        if ($this->string === null) {
            $this->string = $this->name;

            if (!empty($this->value)) {
                $this->string .= sprintf(
                    '="%s"',
                    $this->value
                );
            }
        }

        return $this->string;
    }
}
