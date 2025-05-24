<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Translator\NodeTranslator\ElementTranslator;
use Innmind\Immutable\Map;

final class NodeTranslators
{
    /**
     * @psalm-pure
     *
     * @return Map<int, NodeTranslator>
     */
    public static function defaults(): Map
    {
        /**
         * @var Map<int, NodeTranslator>
         */
        return Map::of(
            [\XML_ELEMENT_NODE, ElementTranslator::of()],
        );
    }
}
