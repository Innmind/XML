<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML;

use Innmind\XML\{
    SelfClosingNode,
    NodeInterface,
    AttributeInterface,
    Attribute
};
use Innmind\Immutable\{
    Map,
    MapInterface
};

class SelfClosingNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new SelfClosingNode('foo')
        );
    }

    public function testName()
    {
        $node = new SelfClosingNode('foo');

        $this->assertSame('foo', $node->name());
    }

    /**
     * @expectedException Innmind\XML\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyName()
    {
        new SelfClosingNode('');
    }

    public function testAttributes()
    {
        $node = new SelfClosingNode(
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
        new SelfClosingNode('foo', new Map('string', 'string'));
    }

    public function testDefaultAttributes()
    {
        $node = new SelfClosingNode('foo');

        $this->assertInstanceOf(MapInterface::class, $node->attributes());
        $this->assertSame('string', (string) $node->attributes()->keyType());
        $this->assertSame(
            AttributeInterface::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new SelfClosingNode(
            'foo',
            new Map('string', AttributeInterface::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new SelfClosingNode(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new SelfClosingNode(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', $expected = new Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testChildren()
    {
        $node = new SelfClosingNode('foo');

        $this->assertTrue(
            $node
                ->children()
                ->equals(
                    new Map('int', NodeInterface::class)
                )
        );
    }

    public function testHasChildren()
    {
        $node = new SelfClosingNode('foo');
        $this->assertFalse($node->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            '',
            (new SelfClosingNode('foo'))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo />',
            (string) new SelfClosingNode('foo')
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo" />',
            (string) new SelfClosingNode(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo'))
            )
        );
    }
}
