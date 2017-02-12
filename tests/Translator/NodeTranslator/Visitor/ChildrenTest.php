<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\NodeTranslator\Visitor\Children,
    Translator\NodeTranslator,
    Translator\NodeTranslators,
    NodeInterface
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class ChildrenTest extends TestCase
{
    public function testNoChildren()
    {
        $document = new \DOMDocument;
        $document->loadXML('<root></root>');

        $children = (new Children(
            new NodeTranslator(
                NodeTranslators::defaults()
            )
        ))($document->childNodes->item(0));

        $this->assertInstanceOf(MapInterface::class, $children);
        $this->assertSame('int', (string) $children->keyType());
        $this->assertSame(NodeInterface::class, (string) $children->valueType());
        $this->assertCount(0, $children);
    }

    public function testChildren()
    {
        $document = new \DOMDocument;
        $document->loadXML('<root><foo/><bar/></root>');

        $children = (new Children(
            new NodeTranslator(
                NodeTranslators::defaults()
            )
        ))($document->childNodes->item(0));

        $this->assertInstanceOf(MapInterface::class, $children);
        $this->assertSame('int', (string) $children->keyType());
        $this->assertSame(NodeInterface::class, (string) $children->valueType());
        $this->assertCount(2, $children);
    }
}
