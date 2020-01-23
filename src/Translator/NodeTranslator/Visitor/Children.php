<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\Translator,
    Node,
};
use Innmind\Immutable\Sequence;

final class Children
{
    private Translator $translate;

    public function __construct(Translator $translate)
    {
        $this->translate = $translate;
    }

    public function __invoke(\DOMNode $node): Sequence
    {
        $children = Sequence::of(Node::class);

        if (!$node->childNodes instanceof \DOMNodeList) {
            return $children;
        }

        foreach ($node->childNodes as $child) {
            $children = ($children)(
                ($this->translate)($child),
            );
        }

        return $children;
    }
}
