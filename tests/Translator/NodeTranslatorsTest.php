<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator;

use Innmind\Xml\Translator\{
    NodeTranslators,
    NodeTranslatorInterface,
    NodeTranslator\DocumentTranslator,
    NodeTranslator\ElementTranslator,
    NodeTranslator\CharacterDataTranslator,
    NodeTranslator\CommentTranslator,
    NodeTranslator\TextTranslator,
    NodeTranslator\EntityReferenceTranslator,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class NodeTranslatorsTest extends TestCase
{
    public function testDefaults()
    {
        $defaults = NodeTranslators::defaults();

        $this->assertInstanceOf(MapInterface::class, $defaults);
        $this->assertSame('int', (string) $defaults->keyType());
        $this->assertSame(
            NodeTranslatorInterface::class,
            (string) $defaults->valueType()
        );
        $this->assertCount(6, $defaults);
        $this->assertInstanceOf(
            DocumentTranslator::class,
            $defaults->get(XML_DOCUMENT_NODE)
        );
        $this->assertInstanceOf(
            ElementTranslator::class,
            $defaults->get(XML_ELEMENT_NODE)
        );
        $this->assertInstanceOf(
            CharacterDataTranslator::class,
            $defaults->get(XML_CDATA_SECTION_NODE)
        );
        $this->assertInstanceOf(
            CommentTranslator::class,
            $defaults->get(XML_COMMENT_NODE)
        );
        $this->assertInstanceOf(
            TextTranslator::class,
            $defaults->get(XML_TEXT_NODE)
        );
        $this->assertInstanceOf(
            EntityReferenceTranslator::class,
            $defaults->get(XML_ENTITY_REF_NODE)
        );
    }
}
