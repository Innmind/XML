<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Translator\NodeTranslator\{
    DocumentTranslator,
    ElementTranslator,
    CharacterDataTranslator,
    CommentTranslator,
    TextTranslator,
    EntityReferenceTranslator,
};
use Innmind\Immutable\Map;

final class NodeTranslators
{
    /**
     * @return Map<int, NodeTranslator>
     */
    public static function defaults(): Map
    {
        /**
         * @var Map<int, NodeTranslator>
         */
        return Map::of(
            [\XML_DOCUMENT_NODE, new DocumentTranslator],
            [\XML_ELEMENT_NODE, new ElementTranslator],
            [\XML_CDATA_SECTION_NODE, new CharacterDataTranslator],
            [\XML_TEXT_NODE, new TextTranslator],
            [\XML_COMMENT_NODE, new CommentTranslator],
            [\XML_ENTITY_REF_NODE, new EntityReferenceTranslator],
        );
    }
}
