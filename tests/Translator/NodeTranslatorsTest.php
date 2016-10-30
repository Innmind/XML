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
    NodeTranslator\TextTranslator
};
use Innmind\Immutable\MapInterface;

class NodeTranslatorsTest extends \PHPUnit_Framework_TestCase
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
        $this->assertCount(5, $defaults);
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
    }
}
