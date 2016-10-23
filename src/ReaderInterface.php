<?php
declare(strict_types = 1);

namespace Innmind\XML;

interface ReaderInterface
{
    public function read(string $content): NodeInterface;
}
