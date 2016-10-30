<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Translator\NodeTranslator\{
    DocumentTranslator,
    ElementTranslator,
    CharacterDataTranslator,
    CommentTranslator,
    TextTranslator
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

final class NodeTranslators
{
    private static $defaults;

    /**
     * @return MapInterface<int, NodeTranslatorInterface>
     */
    public static function defaults(): MapInterface
    {
        if (!self::$defaults) {
            self::$defaults = (new Map('int', NodeTranslatorInterface::class))
                ->put(XML_DOCUMENT_NODE, new DocumentTranslator)
                ->put(XML_ELEMENT_NODE, new ElementTranslator)
                ->put(XML_CDATA_SECTION_NODE, new CharacterDataTranslator)
                ->put(XML_TEXT_NODE, new TextTranslator)
                ->put(XML_COMMENT_NODE, new CommentTranslator);
        }

        return self::$defaults;
    }
}
