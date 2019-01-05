<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Stream\Readable;

interface Reader
{
    public function __invoke(Readable $content): Node;
}
