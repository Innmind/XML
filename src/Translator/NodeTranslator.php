<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Node;

interface NodeTranslator
{
    public function translate(
        \DOMNode $node,
        Translator $translator
    ): Node;
}
