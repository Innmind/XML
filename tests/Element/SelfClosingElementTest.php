<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element\SelfClosingElement,
    NodeInterface,
    AttributeInterface,
    Attribute,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};
use PHPUnit\Framework\TestCase;

class SelfClosingElementTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new SelfClosingElement('foo')
        );
    }

    public function testName()
    {
        $node = new SelfClosingElement('foo');

        $this->assertSame('foo', $node->name());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyName()
    {
        new SelfClosingElement('');
    }

    public function testAttributes()
    {
        $node = new SelfClosingElement(
            'foo',
            $expected = new Map('string', AttributeInterface::class)
        );

        $this->assertSame($expected, $node->attributes());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidAttributes()
    {
        new SelfClosingElement('foo', new Map('string', 'string'));
    }

    public function testDefaultAttributes()
    {
        $node = new SelfClosingElement('foo');

        $this->assertInstanceOf(MapInterface::class, $node->attributes());
        $this->assertSame('string', (string) $node->attributes()->keyType());
        $this->assertSame(
            AttributeInterface::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new SelfClosingElement(
            'foo',
            new Map('string', AttributeInterface::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', $expected = new Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testRemoveAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        );

        $node2 = $node->removeAttribute('foo');

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertSame($node->children(), $node2->children());
        $this->assertNotSame($node->attributes(), $node2->attributes());
        $this->assertCount(2, $node->attributes());
        $this->assertCount(1, $node2->attributes());
        $this->assertTrue($node->attributes()->contains('foo'));
        $this->assertTrue($node->attributes()->contains('bar'));
        $this->assertFalse($node2->attributes()->contains('foo'));
        $this->assertTrue($node2->attributes()->contains('bar'));
        $this->assertSame(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar')
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\OutOfBoundsException
     */
    public function testThrowWhenRemovingUnknownAttribute()
    {
        (new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        ))->removeAttribute('baz');
    }

    public function testReplaceAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        );

        $node2 = $node->replaceAttribute(
            $attribute = new Attribute('foo', 'baz')
        );

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertSame($node->children(), $node2->children());
        $this->assertNotSame($node->attributes(), $node2->attributes());
        $this->assertCount(2, $node->attributes());
        $this->assertCount(2, $node2->attributes());
        $this->assertTrue($node->attributes()->contains('foo'));
        $this->assertTrue($node->attributes()->contains('bar'));
        $this->assertTrue($node2->attributes()->contains('foo'));
        $this->assertTrue($node2->attributes()->contains('bar'));
        $this->assertSame(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar')
        );
        $this->assertSame(
            $attribute,
            $node2->attributes()->get('foo')
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\OutOfBoundsException
     */
    public function testThrowWhenReplacingUnknownAttribute()
    {
        (new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        ))->replaceAttribute(
            new Attribute('baz')
        );
    }

    public function testAddAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        );

        $node2 = $node->addAttribute(
            $attribute = new Attribute('baz', 'baz')
        );

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertSame($node->children(), $node2->children());
        $this->assertNotSame($node->attributes(), $node2->attributes());
        $this->assertCount(2, $node->attributes());
        $this->assertCount(3, $node2->attributes());
        $this->assertTrue($node->attributes()->contains('foo'));
        $this->assertTrue($node->attributes()->contains('bar'));
        $this->assertTrue($node2->attributes()->contains('foo'));
        $this->assertTrue($node2->attributes()->contains('bar'));
        $this->assertSame(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar')
        );
        $this->assertSame(
            $node->attributes()->get('foo'),
            $node2->attributes()->get('foo')
        );
        $this->assertSame(
            $attribute,
            $node2->attributes()->get('baz')
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAttributeAlreadyExists()
    {
        (new SelfClosingElement(
            'foo',
            (new Map('string', AttributeInterface::class))
                ->put('foo', new Attribute('foo'))
                ->put('bar', new Attribute('bar'))
        ))->addAttribute(new Attribute('foo', 'baz'));
    }

    public function testChildren()
    {
        $node = new SelfClosingElement('foo');

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
        $node = new SelfClosingElement('foo');
        $this->assertFalse($node->hasChildren());
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenRemovingChild()
    {
        (new SelfClosingElement('foo'))->removeChild(0);
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenReplacingChild()
    {
        (new SelfClosingElement('foo'))->replaceChild(
            0,
            $this->createMock(NodeInterface::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenPrependingChild()
    {
        (new SelfClosingElement('foo'))->prependChild(
            $this->createMock(NodeInterface::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAppendingChild()
    {
        (new SelfClosingElement('foo'))->appendChild(
            $this->createMock(NodeInterface::class)
        );
    }

    public function testContent()
    {
        $this->assertSame(
            '',
            (new SelfClosingElement('foo'))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo/>',
            (string) new SelfClosingElement('foo')
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"/>',
            (string) new SelfClosingElement(
                'foo',
                (new Map('string', AttributeInterface::class))
                    ->put('bar', new Attribute('bar', 'baz'))
                    ->put('baz', new Attribute('baz', 'foo'))
            )
        );
    }
}
