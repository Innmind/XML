<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\NodeTranslator,
    NodeInterface,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

final class Children
{
    private $translator;

    public function __construct(NodeTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(\DOMNode $node): MapInterface
    {
        $children = new Map('int', NodeInterface::class);

        if (!$node->childNodes instanceof \DOMNodeList) {
            return $children;
        }

        foreach ($node->childNodes as $child) {
            $children = $children->put(
                $children->size(),
                $this->translator->translate($child)
            );
        }

        return $children;
    }
}
