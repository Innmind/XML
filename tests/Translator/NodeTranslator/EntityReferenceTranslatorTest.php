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
        $translator = new EntityReferenceTranslator;
        $node = $translator->translate(
            new \DOMEntityReference('gt'),
            new Translator(
                new Map('int', NodeTranslator::class)
            )
        );

        $this->assertInstanceOf(EntityReference::class, $node);
        $this->assertSame('gt', $node->content());
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new EntityReferenceTranslator)->translate(
            new \DOMNode,
            new Translator(
                new Map('int', NodeTranslator::class)
            )
        );
    }
}
