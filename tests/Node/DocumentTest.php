<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Document,
    Node\Document\Version,
    Node\Document\Type,
    Node\Document\Encoding,
    Node,
    Element\Element,
    Element\SelfClosingElement,
    Exception\OutOfBoundsException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Document(new Version(1))
        );
    }

    public function testVersion()
    {
        $document = new Document(
            $version = new Version(1)
        );

        $this->assertSame($version, $document->version());
    }

    public function testType()
    {
        $document = new Document(new Version(1));
        $this->assertFalse($document->hasType());

        $document = new Document(
            new Version(1),
            $type = new Type('html')
        );
        $this->assertTrue($document->hasType());
        $this->assertSame($type, $document->type());
    }

    public function testChildren()
    {
        $document = new Document(
            new Version(1),
            null,
            $children = Map::of('int', Node::class)
        );

        $this->assertSame($children, $document->children());
    }

    public function testDefaultChildren()
    {
        $document = new Document(new Version(1));

        $this->assertInstanceOf(
            Map::class,
            $document->children()
        );
        $this->assertSame('int', (string) $document->children()->keyType());
        $this->assertSame(
            Node::class,
            (string) $document->children()->valueType()
        );
    }

    public function testThrowWhenInvalidChildren()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 3 must be of type Map<int, Innmind\Xml\Node>');

        new Document(
            new Version(1),
            null,
            Map::of('string', 'string')
        );
    }

    public function testEncoding()
    {
        $document = new Document(new Version(1));
        $this->assertFalse($document->encodingIsSpecified());

        $document = new Document(
            new Version(1),
            null,
            null,
            $encoding = new Encoding('utf-8')
        );
        $this->assertTrue($document->encodingIsSpecified());
        $this->assertSame($encoding, $document->encoding());
    }

    public function testContentWithoutChildren()
    {
        $this->assertSame(
            '',
            (new Document(new Version(1)))->content()
        );
    }

    public function testContentWithChildren()
    {
        $this->assertSame(
            '<foo></foo>',
            (new Document(
                new Version(1),
                null,
                Map::of('int', Node::class)
                    (0, new Element('foo'))
            ))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<?xml version="2.1"?>'."\n",
            (new Document(new Version(2, 1)))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n",
            (new Document(
                new Version(2, 1),
                null,
                null,
                new Encoding('utf-8')
            ))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n",
            (new Document(
                new Version(2, 1),
                new Type('html'),
                null,
                new Encoding('utf-8')
            ))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n".'<foo/>',
            (new Document(
                new Version(2, 1),
                new Type('html'),
                Map::of('int', Node::class)
                    (0, new SelfClosingElement('foo')),
                new Encoding('utf-8')
            ))->toString(),
        );
    }

    public function testRemoveChild()
    {
        $document = new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        );

        $document2 = $document->removeChild(1);

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertCount(3, $document->children());
        $this->assertCount(2, $document2->children());
        $this->assertSame(
            $document->children()->get(0),
            $document2->children()->get(0)
        );
        $this->assertSame(
            $document->children()->get(2),
            $document2->children()->get(1)
        );
    }

    public function testThrowWhenRemovingUnknownChild()
    {
        $this->expectException(OutOfBoundsException::class);

        (new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        ))->removeChild(3);
    }

    public function testReplaceChild()
    {
        $document = new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        );

        $document2 = $document->replaceChild(
            1,
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertCount(3, $document->children());
        $this->assertCount(3, $document2->children());
        $this->assertSame(
            $document->children()->get(0),
            $document2->children()->get(0)
        );
        $this->assertNotSame(
            $document->children()->get(1),
            $document2->children()->get(1)
        );
        $this->assertSame($node, $document2->children()->get(1));
        $this->assertSame(
            $document->children()->get(2),
            $document2->children()->get(2)
        );
    }

    public function testThrowWhenReplacingUnknownChild()
    {
        $this->expectException(OutOfBoundsException::class);

        (new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        ))->replaceChild(
            3,
            $this->createMock(Node::class)
        );
    }

    public function testPrependChild()
    {
        $document = new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        );

        $document2 = $document->prependChild(
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertNotSame($document->children(), $document2->children());
        $this->assertCount(3, $document->children());
        $this->assertCount(4, $document2->children());
        $this->assertSame(
            $node,
            $document2->children()->get(0)
        );
        $this->assertSame(
            $document->children()->get(0),
            $document2->children()->get(1)
        );
        $this->assertSame(
            $document->children()->get(1),
            $document2->children()->get(2)
        );
        $this->assertSame(
            $document->children()->get(2),
            $document2->children()->get(3)
        );
    }

    public function testAppendChild()
    {
        $document = new Document(
            new Version(1),
            new Type('html'),
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz')),
            new Encoding('utf-8')
        );

        $document2 = $document->appendChild(
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertNotSame($document->children(), $document2->children());
        $this->assertCount(3, $document->children());
        $this->assertCount(4, $document2->children());
        $this->assertSame(
            $document->children()->get(0),
            $document2->children()->get(0)
        );
        $this->assertSame(
            $document->children()->get(1),
            $document2->children()->get(1)
        );
        $this->assertSame(
            $document->children()->get(2),
            $document2->children()->get(2)
        );
        $this->assertSame(
            $node,
            $document2->children()->get(3)
        );
    }
}
