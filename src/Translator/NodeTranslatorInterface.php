<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\NodeInterface;

interface NodeTranslatorInterface
{
    public function translate(\DOMNode $node): NodeInterface;
}
