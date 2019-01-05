<?php
declare(strict_types = 1);

namespace Innmind\Xml;

interface Attribute
{
    public function name(): string;
    public function value(): string;
    public function __toString(): string;
}
