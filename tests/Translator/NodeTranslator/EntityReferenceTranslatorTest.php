<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\EntityReferenceTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Node\EntityReference,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class EntityReferenceTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
            new EntityReferenceTranslator
        );
    }

    public function testTranslate()
    {
        $translator = new EntityReferenceTranslator;
        $node = $translator->translate(
            new \DOMEntityReference('gt'),
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );

        $this->assertInstanceOf(EntityReference::class, $node);
        $this->assertSame('gt', $node->content());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new EntityReferenceTranslator)->translate(
            new \DOMNode,
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
