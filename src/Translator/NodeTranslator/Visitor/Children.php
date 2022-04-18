<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\Translator,
    Node,
};
use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 */
final class Children
{
    private Translator $translate;

    public function __construct(Translator $translate)
    {
        $this->translate = $translate;
    }

    /**
     * @return Sequence<Node>
     */
    public function __invoke(\DOMNode $node): Sequence
    {
        /** @var Sequence<Node> */
        $children = Sequence::of();

        foreach ($node->childNodes as $child) {
            $children = ($children)(
                ($this->translate)($child),
            );
        }

        return $children;
    }
}
