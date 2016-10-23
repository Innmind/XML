<?php
declare(strict_types = 1);

namespace Innmind\XML;

interface AttributeInterface
{
    public function name(): string;
    public function value(): string;
    public function __toString(): string;
}
