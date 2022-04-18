<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Node;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
interface NodeTranslator
{
    /**
     * @return Maybe<Node>
     */
    public function __invoke(\DOMNode $node, Translator $translate): Maybe;
}
