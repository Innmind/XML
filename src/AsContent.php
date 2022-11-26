<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Filesystem\File\Content;

interface AsContent
{
    public function asContent(): Content;
}
