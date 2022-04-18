<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Stream\Readable;
use Innmind\Immutable\Maybe;

interface Reader
{
    /**
     * @return Maybe<Node>
     */
    public function __invoke(Readable $content): Maybe;
}
