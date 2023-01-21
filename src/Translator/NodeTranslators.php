<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator;

use Innmind\Xml\Translator\NodeTranslator\{
    DocumentTranslator,
    ElementTranslator,
    CharacterDataTranslator,
    CommentTranslator,
    ProcessingInstructionTranslator,
    TextTranslator,
    EntityReferenceTranslator,
};
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
            [\XML_DOCUMENT_NODE, DocumentTranslator::of()],
            [\XML_ELEMENT_NODE, ElementTranslator::of()],
            [\XML_CDATA_SECTION_NODE, CharacterDataTranslator::of()],
            [\XML_TEXT_NODE, TextTranslator::of()],
            [\XML_COMMENT_NODE, CommentTranslator::of()],
            [\XML_PI_NODE, ProcessingInstructionTranslator::of()],
            [\XML_ENTITY_REF_NODE, EntityReferenceTranslator::of()],
        );
    }
}
