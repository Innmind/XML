<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Node;

interface NodeTranslator
{
    public function __invoke(
        \DOMNode $node,
        Translator $translate
    ): Node;
}
