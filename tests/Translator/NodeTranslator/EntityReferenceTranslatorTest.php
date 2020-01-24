<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\EntityReferenceTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\EntityReference,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class EntityReferenceTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new EntityReferenceTranslator
        );
    }

    public function testTranslate()
    {
        $translate = new EntityReferenceTranslator;
        $node = $translate(
            new \DOMEntityReference('gt'),
            new Translator(
                Map::of('int', NodeTranslator::class)
            )
        );

        $this->assertInstanceOf(EntityReference::class, $node);
        $this->assertSame('gt', $node->content());
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new EntityReferenceTranslator)(
            new \DOMNode,
            new Translator(
                Map::of('int', NodeTranslator::class)
            )
        );
    }
}
