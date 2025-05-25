<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

/**
 * @internal
 * @psalm-immutable
 */
interface Implementation
{
    public function content(): string;
    public function toString(): string;
}
