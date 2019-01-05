<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element\Element,
    Node,
    Attribute,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Element('foo')
        );
    }

    public function testName()
    {
        $node = new Element('foo');

        $this->assertSame('foo', $node->name());
    }

    /**
     * @expectedException Innmind\Xml\Exception\DomainException
     */
    public function testThrowWhenEmptyName()
    {
        new Element('');
    }

    public function testAttributes()
    {
        $node = new Element(
            'foo',
            $expected = new Map('string', Attribute::class)
        );

        $this->assertSame($expected, $node->attributes());
    }

    /**
     * @expectedException TypeError
     * @expectedExceptionMessage Argument 2 must be of type MapInterface<string, Innmind\Xml\Attribute>
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
            Attribute::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new Element(
            'foo',
            new Map('string', Attribute::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', $expected = new Attribute\Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testRemoveAttribute()
    {
        $node = new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        );

        $node2 = $node->removeAttribute('foo');

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Element::class, $node2);
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
        (new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        ))->removeAttribute('baz');
    }

    public function testReplaceAttribute()
    {
        $node = new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        );

        $node2 = $node->replaceAttribute(
            $attribute = new Attribute\Attribute('foo', 'baz')
        );

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Element::class, $node2);
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
        (new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        ))->replaceAttribute(
            new Attribute\Attribute('baz')
        );
    }

    public function testAddAttribute()
    {
        $node = new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        );

        $node2 = $node->addAttribute(
            $attribute = new Attribute\Attribute('baz', 'baz')
        );

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Element::class, $node2);
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
        (new Element(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute\Attribute('foo'))
                ('bar', new Attribute\Attribute('bar'))
        ))->addAttribute(new Attribute\Attribute('foo', 'baz'));
    }

    public function testChildren()
    {
        $node = new Element(
            'foo',
            null,
            $expected = new Map('int', Node::class)
        );

        $this->assertSame($expected, $node->children());
    }

    public function testDefaultChildren()
    {
        $node = new Element('foo');

        $this->assertInstanceOf(MapInterface::class, $node->children());
        $this->assertSame('int', (string) $node->children()->keyType());
        $this->assertSame(
            Node::class,
            (string) $node->children()->valueType()
        );
    }

    public function testHasChildren()
    {
        $node = new Element(
            'foo',
            null,
            Map::of('int', Node::class)
                (0, new Element('bar'))
        );
        $this->assertTrue($node->hasChildren());

        $this->assertFalse((new Element('foo'))->hasChildren());
    }

    public function testRemoveChild()
    {
        $element = new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        );

        $element2 = $element->removeChild(1);

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertCount(3, $element->children());
        $this->assertCount(2, $element2->children());
        $this->assertSame(
            $element->children()->get(0),
            $element2->children()->get(0)
        );
        $this->assertSame(
            $element->children()->get(2),
            $element2->children()->get(1)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\OutOfBoundsException
     */
    public function testThrowWhenRemovingUnknownChild()
    {
        (new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        ))->removeChild(3);
    }

    public function testReplaceChild()
    {
        $element = new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        );

        $element2 = $element->replaceChild(
            1,
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertCount(3, $element->children());
        $this->assertCount(3, $element2->children());
        $this->assertSame(
            $element->children()->get(0),
            $element2->children()->get(0)
        );
        $this->assertNotSame(
            $element->children()->get(1),
            $element2->children()->get(1)
        );
        $this->assertSame($node, $element2->children()->get(1));
        $this->assertSame(
            $element->children()->get(2),
            $element2->children()->get(2)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\OutOfBoundsException
     */
    public function testThrowWhenReplacingUnknownChild()
    {
        (new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        ))->replaceChild(
            3,
            $this->createMock(Node::class)
        );
    }

    public function testPrependChild()
    {
        $element = new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        );

        $element2 = $element->prependChild(
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertNotSame($element->children(), $element2->children());
        $this->assertCount(3, $element->children());
        $this->assertCount(4, $element2->children());
        $this->assertSame(
            $node,
            $element2->children()->get(0)
        );
        $this->assertSame(
            $element->children()->get(0),
            $element2->children()->get(1)
        );
        $this->assertSame(
            $element->children()->get(1),
            $element2->children()->get(2)
        );
        $this->assertSame(
            $element->children()->get(2),
            $element2->children()->get(3)
        );
    }

    public function testAopendChild()
    {
        $element = new Element(
            'foobar',
            null,
            Map::of('int', Node::class)
                (0, new Element('foo'))
                (1, new Element('bar'))
                (2, new Element('baz'))
        );

        $element2 = $element->appendChild(
            $node = $this->createMock(Node::class)
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertNotSame($element->children(), $element2->children());
        $this->assertCount(3, $element->children());
        $this->assertCount(4, $element2->children());
        $this->assertSame(
            $element->children()->get(0),
            $element2->children()->get(0)
        );
        $this->assertSame(
            $element->children()->get(1),
            $element2->children()->get(1)
        );
        $this->assertSame(
            $element->children()->get(2),
            $element2->children()->get(2)
        );
        $this->assertSame(
            $node,
            $element2->children()->get(3)
        );
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
            Map::of('int', Node::class)
                (0, new Element('bar'))
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
                Map::of('string', Attribute::class)
                    ('bar', new Attribute\Attribute('bar', 'baz'))
                    ('baz', new Attribute\Attribute('baz', 'foo'))
            )
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"><bar></bar><baz></baz></foo>',
            (string) new Element(
                'foo',
                Map::of('string', Attribute::class)
                    ('bar', new Attribute\Attribute('bar', 'baz'))
                    ('baz', new Attribute\Attribute('baz', 'foo')),
                Map::of('int', Node::class)
                    (0, new Element('bar'))
                    (1, new Element('baz'))
            )
        );
    }
}
