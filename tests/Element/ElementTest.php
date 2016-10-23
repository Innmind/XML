<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Element;

use Innmind\XML\{
    Element\Element,
    NodeInterface,
    AttributeInterface,
    Attribute
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new Element('foo')
        );

        $dom = new \DOMDocument;
        $dom->loadXML(<<<XML
<!DOCTYPE HTML>
<root></root>
XML
        );
        dump($dom->doctype);
    }

    public function testName()
    {
        $node = new Element('foo');

        $this->assertSame('foo', $node->name());
    }

    /**
     * @expectedException Innmind\XML\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyName()
    {
        new Element('');
    }

    public function testAttributes()
    {
        $node = new Element(
            'foo',
            $expected = new Map('string', AttributeInterface::class)
        );

        $this->assertSame($expected, $node->attributes());
    }

    /**
     * @expectedException Innmind\XML\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidAttributes()
    {
        new Element('foo', new Map('string', 'string'));
    }

    public function testDefaultAttributes()
    {
        $node = new Element('foo');

        $this->assertInstanceOf(MapInterface::class, $node->attributes());
        $this->assertSame('string', (string) $node->attributes()->keyType());
        $this->assertSame(
            AttributeInterface::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new Element(
            'foo',
            new Map('string', AttributeInterface::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new Element(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new Element(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', $expected = new Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testChildren()
    {
        $node = new Element(
            'foo',
            null,
            $expected = new Map('int', NodeInterface::class)
        );

        $this->assertSame($expected, $node->children());
    }

    public function testDefaultChildren()
    {
        $node = new Element('foo');

        $this->assertInstanceOf(MapInterface::class, $node->children());
        $this->assertSame('int', (string) $node->children()->keyType());
        $this->assertSame(
            NodeInterface::class,
            (string) $node->children()->valueType()
        );
    }

    public function testHasChildren()
    {
        $node = new Element(
            'foo',
            null,
            (new Map('int', NodeInterface::class))
                ->put(0, new Element('bar'))
        );
        $this->assertTrue($node->hasChildren());

        $this->assertFalse((new Element('foo'))->hasChildren());
    }

    public function testContentWithoutChildren()
    {
        $this->assertSame(
            '',
            (new Element('foo'))->content()
        );
    }

    public function testContentWithChildren()
    {
        $node = new Element(
            'foo',
            null,
            (new Map('int', NodeInterface::class))
                ->put(0, new Element('bar'))
        );

        $this->assertSame(
            '<bar></bar>',
            $node->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo></foo>',
            (string) new Element('foo')
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"></foo>',
            (string) new Element(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo'))
            )
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"><bar></bar><baz></baz></foo>',
            (string) new Element(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo')),
                (new Map('int', NodeInterface::class))
                    ->put(0, new Element('bar'))
                    ->put(1, new Element('baz'))
            )
        );
    }
}
