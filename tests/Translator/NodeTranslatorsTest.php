<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator;

use Innmind\Xml\Translator\{
    NodeTranslators,
    NodeTranslator\DocumentTranslator,
    NodeTranslator\ElementTranslator,
    NodeTranslator\CharacterDataTranslator,
    NodeTranslator\CommentTranslator,
    NodeTranslator\ProcessingInstructionTranslator,
    NodeTranslator\TextTranslator,
    NodeTranslator\EntityReferenceTranslator,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class NodeTranslatorsTest extends TestCase
{
    public function testDefaults()
    {
        $defaults = NodeTranslators::defaults();

        $this->assertInstanceOf(Map::class, $defaults);
        $this->assertCount(7, $defaults);
        $this->assertInstanceOf(
            DocumentTranslator::class,
            $defaults->get(\XML_DOCUMENT_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            ElementTranslator::class,
            $defaults->get(\XML_ELEMENT_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            CharacterDataTranslator::class,
            $defaults->get(\XML_CDATA_SECTION_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            CommentTranslator::class,
            $defaults->get(\XML_COMMENT_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            ProcessingInstructionTranslator::class,
            $defaults->get(\XML_PI_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            TextTranslator::class,
            $defaults->get(\XML_TEXT_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
        $this->assertInstanceOf(
            EntityReferenceTranslator::class,
            $defaults->get(\XML_ENTITY_REF_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
    }
}
