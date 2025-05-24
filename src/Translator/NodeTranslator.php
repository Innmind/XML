<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\{
    Node,
    Element,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
interface NodeTranslator
{
    /**
     * @return Maybe<Node|Element>
     */
    public function __invoke(\DOMNode $node, Translator $translate): Maybe;
}
