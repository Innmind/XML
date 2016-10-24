<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Node;

use Innmind\XML\{
    Node\Document,
    Node\Document\Version,
    Node\Document\Type,
    Node\Document\Encoding,
    NodeInterface,
    Element\Element,
    Element\SelfClosingElement
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
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
            $children = new Map('int', NodeInterface::class)
        );

        $this->assertSame($children, $document->children());
    }

    public function testDefaultChildren()
    {
        $document = new Document(new Version(1));

        $this->assertInstanceOf(
            MapInterface::class,
            $document->children()
        );
        $this->assertSame('int', (string) $document->children()->keyType());
        $this->assertSame(
            NodeInterface::class,
            (string) $document->children()->valueType()
        );
    }

    /**
     * @expectedException Innmind\XML\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidChildren()
    {
        new Document(
            new Version(1),
            null,
            new Map('string', 'string')
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
                (new Map('int', NodeInterface::class))
                    ->put(0, new Element('foo'))
            ))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<?xml version="2.1"?>'."\n",
            (string) new Document(new Version(2, 1))
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n",
            (string) new Document(
                new Version(2, 1),
                null,
                null,
                new Encoding('utf-8')
            )
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n",
            (string) new Document(
                new Version(2, 1),
                new Type('html'),
                null,
                new Encoding('utf-8')
            )
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n".'<foo />',
            (string) new Document(
                new Version(2, 1),
                new Type('html'),
                (new Map('int', NodeInterface::class))
                    ->put(0, new SelfClosingElement('foo')),
                new Encoding('utf-8')
            )
        );
    }
}
