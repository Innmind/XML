<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Filesystem\File\Content;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
interface Reader
{
    /**
     * @return Maybe<Node>
     */
    public function __invoke(Content $content): Maybe;
}
