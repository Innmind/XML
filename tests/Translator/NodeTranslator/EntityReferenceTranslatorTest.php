<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\EntityReferenceTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\EntityReference,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class EntityReferenceTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new EntityReferenceTranslator,
        );
    }

    public function testTranslate()
    {
        $translate = new EntityReferenceTranslator;
        $node = $translate(
            new \DOMEntityReference('gt'),
            new Translator(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(EntityReference::class, $node);
        $this->assertSame('gt', $node->content());
    }

    public function testReturnNothingWhenInvalidNode()
    {
        $this->assertNull((new EntityReferenceTranslator)(
            new \DOMNode,
            new Translator(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
