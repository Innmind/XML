<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Filesystem\StreamInterface;

interface ReaderInterface
{
    public function read(StreamInterface $content): NodeInterface;
}
