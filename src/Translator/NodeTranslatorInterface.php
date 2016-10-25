<?php
declare(strict_types = 1);

namespace Innmind\XML\Translator;

use Innmind\XML\NodeInterface;

interface NodeTranslatorInterface
{
    public function translate(\DOMNode $node): NodeInterface;
}
