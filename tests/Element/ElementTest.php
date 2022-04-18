<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element\Element,
    Node,
    Attribute,
    Exception\DomainException,
    Exception\LogicException,
    Exception\OutOfBoundsException,
};
use Innmind\Immutable\{
    Map,
    Set,
    Sequence,
};
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Element('foo'),
        );
    }

    public function testName()
    {
        $node = new Element('foo');

        $this->assertSame('foo', $node->name());
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        new Element('');
    }

    public function testDefaultAttributes()
    {
        $node = new Element('foo');

        $this->assertInstanceOf(Map::class, $node->attributes());
    }

    public function testAttribute()
    {
        $node = new Element(
            'foo',
            Set::of($expected = Attribute::of('foo')),
        );

        $this->assertSame($expected, $node->attribute('foo')->match(
            static fn($attribute) => $attribute,
            static fn() => null,
        ));
    }

    public function testRemoveAttribute()
    {
        $node = new Element(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
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
        $this->assertEquals(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar'),
        );
    }

    public function testDoNothingWhenRemovingUnknownAttribute()
    {
        $element = new Element(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $this->assertSame($element, $element->removeAttribute('baz'));
    }

    public function testReplaceAttribute()
    {
        $node = new Element(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $node2 = $node->addAttribute(
            $attribute = Attribute::of('foo', 'baz'),
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
        $this->assertEquals(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar'),
        );
        $this->assertSame(
            $attribute,
            $node2->attributes()->get('foo')->match(
                static fn($attribute) => $attribute,
                static fn() => null,
            ),
        );
    }

    public function testAddAttribute()
    {
        $node = new Element(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $node2 = $node->addAttribute(
            $attribute = Attribute::of('baz', 'baz'),
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
        $this->assertEquals(
            $node->attributes()->get('bar'),
            $node2->attributes()->get('bar'),
        );
        $this->assertEquals(
            $node->attributes()->get('foo'),
            $node2->attributes()->get('foo'),
        );
        $this->assertSame(
            $attribute,
            $node2->attributes()->get('baz')->match(
                static fn($attribute) => $attribute,
                static fn() => null,
            ),
        );
    }

    public function testDefaultChildren()
    {
        $node = new Element('foo');

        $this->assertInstanceOf(Sequence::class, $node->children());
    }

    public function testHasChildren()
    {
        $node = new Element(
            'foo',
            null,
            new Element('bar'),
        );
        $this->assertFalse($node->children()->empty());

        $this->assertTrue((new Element('foo'))->children()->empty());
    }

    public function testRemoveChild()
    {
        $element = new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        );

        $element2 = $element->removeChild(1);

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertCount(3, $element->children());
        $this->assertCount(2, $element2->children());
        $this->assertEquals(
            $element->children()->get(0),
            $element2->children()->get(0),
        );
        $this->assertEquals(
            $element->children()->get(2),
            $element2->children()->get(1),
        );
    }

    public function testThrowWhenRemovingUnknownChild()
    {
        $this->expectException(OutOfBoundsException::class);

        (new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        ))->removeChild(3);
    }

    public function testReplaceChild()
    {
        $element = new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        );

        $element2 = $element->replaceChild(
            1,
            $node = $this->createMock(Node::class),
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertCount(3, $element->children());
        $this->assertCount(3, $element2->children());
        $this->assertEquals(
            $element->children()->get(0),
            $element2->children()->get(0),
        );
        $this->assertNotEquals(
            $element->children()->get(1),
            $element2->children()->get(1),
        );
        $this->assertSame(
            $node,
            $element2->children()->get(1)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $element->children()->get(2),
            $element2->children()->get(2),
        );
    }

    public function testThrowWhenReplacingUnknownChild()
    {
        $this->expectException(OutOfBoundsException::class);

        (new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        ))->replaceChild(
            3,
            $this->createMock(Node::class),
        );
    }

    public function testPrependChild()
    {
        $element = new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        );

        $element2 = $element->prependChild(
            $node = $this->createMock(Node::class),
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
            $element2->children()->get(0)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $element->children()->get(0),
            $element2->children()->get(1),
        );
        $this->assertEquals(
            $element->children()->get(1),
            $element2->children()->get(2),
        );
        $this->assertEquals(
            $element->children()->get(2),
            $element2->children()->get(3),
        );
    }

    public function testAppendChild()
    {
        $element = new Element(
            'foobar',
            null,
            new Element('foo'),
            new Element('bar'),
            new Element('baz'),
        );

        $element2 = $element->appendChild(
            $node = $this->createMock(Node::class),
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertNotSame($element->children(), $element2->children());
        $this->assertCount(3, $element->children());
        $this->assertCount(4, $element2->children());
        $this->assertEquals(
            $element->children()->get(0),
            $element2->children()->get(0),
        );
        $this->assertEquals(
            $element->children()->get(1),
            $element2->children()->get(1),
        );
        $this->assertEquals(
            $element->children()->get(2),
            $element2->children()->get(2),
        );
        $this->assertSame(
            $node,
            $element2->children()->get(3)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
    }

    public function testContentWithoutChildren()
    {
        $this->assertSame(
            '',
            (new Element('foo'))->content(),
        );
    }

    public function testContentWithChildren()
    {
        $node = new Element(
            'foo',
            null,
            new Element('bar'),
        );

        $this->assertSame(
            '<bar></bar>',
            $node->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo></foo>',
            (new Element('foo'))->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"></foo>',
            (new Element(
                'foo',
                Set::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
            ))->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"><bar></bar><baz></baz></foo>',
            (new Element(
                'foo',
                Set::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
                new Element('bar'),
                new Element('baz'),
            ))->toString(),
        );
    }
}
