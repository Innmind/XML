<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\Translator,
    Node,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class Children
{
    private Translator $translate;

    public function __construct(Translator $translate)
    {
        $this->translate = $translate;
    }

    public function __invoke(\DOMNode $node): MapInterface
    {
        $children = new Map('int', Node::class);

        if (!$node->childNodes instanceof \DOMNodeList) {
            return $children;
        }

        foreach ($node->childNodes as $child) {
            $children = $children->put(
                $children->size(),
                ($this->translate)($child)
            );
        }

        return $children;
    }
}
