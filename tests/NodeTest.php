<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML;

use Innmind\XML\{
    Node,
    NodeInterface,
    AttributeInterface,
    Attribute
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new Node('foo')
        );
    }

    public function testName()
    {
        $node = new Node('foo');

        $this->assertSame('foo', $node->name());
    }

    /**
     * @expectedException Innmind\XML\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyName()
    {
        new Node('');
    }

    public function testAttributes()
    {
        $node = new Node(
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
        new Node('foo', new Map('string', 'string'));
    }

    public function testDefaultAttributes()
    {
        $node = new Node('foo');

        $this->assertInstanceOf(MapInterface::class, $node->attributes());
        $this->assertSame('string', (string) $node->attributes()->keyType());
        $this->assertSame(
            AttributeInterface::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new Node(
            'foo',
            new Map('string', AttributeInterface::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new Node(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new Node(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', $expected = new Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testChildren()
    {
        $node = new Node(
            'foo',
            null,
            $expected = new Map('int', NodeInterface::class)
        );

        $this->assertSame($expected, $node->children());
    }

    public function testDefaultChildren()
    {
        $node = new Node('foo');

        $this->assertInstanceOf(MapInterface::class, $node->children());
        $this->assertSame('int', (string) $node->children()->keyType());
        $this->assertSame(
            NodeInterface::class,
            (string) $node->children()->valueType()
        );
    }

    public function testHasChildren()
    {
        $node = new Node(
            'foo',
            null,
            (new Map('int', NodeInterface::class))
                ->put(0, new Node('bar'))
        );
        $this->assertTrue($node->hasChildren());

        $this->assertFalse((new Node('foo'))->hasChildren());
    }

    public function testContentWithoutChildren()
    {
        $this->assertSame(
            '',
            (new Node('foo'))->content()
        );
    }

    public function testContentWithChildren()
    {
        $node = new Node(
            'foo',
            null,
            (new Map('int', NodeInterface::class))
                ->put(0, new Node('bar'))
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
            (string) new Node('foo')
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"></foo>',
            (string) new Node(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo'))
            )
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"><bar></bar><baz></baz></foo>',
            (string) new Node(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo')),
                (new Map('int', NodeInterface::class))
                    ->put(0, new Node('bar'))
                    ->put(1, new Node('baz'))
            )
        );
    }
}
