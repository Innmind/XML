<?php
declare(strict_types = 1);

namespace Innmind\XML;

use Innmind\Filesystem\FileInterface;

interface WriterInterface
{
    public function write(NodeInterface $node): FileInterface;
}
