<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Stream\Readable;

interface ReaderInterface
{
    public function read(Readable $content): NodeInterface;
}
