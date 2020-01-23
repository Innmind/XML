<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element\SelfClosingElement,
    Node,
    Attribute,
    Exception\DomainException,
    Exception\LogicException,
    Exception\OutOfBoundsException,
};
use Innmind\Immutable\{
    Map,
    Sequence,
};
use PHPUnit\Framework\TestCase;

class SelfClosingElementTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new SelfClosingElement('foo')
        );
    }

    public function testName()
    {
        $node = new SelfClosingElement('foo');

        $this->assertSame('foo', $node->name());
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        new SelfClosingElement('');
    }

    public function testAttributes()
    {
        $node = new SelfClosingElement(
            'foo',
            $expected = Map::of('string', Attribute::class)
        );

        $this->assertSame($expected, $node->attributes());
    }

    public function testThrowWhenInvalidAttributes()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type Map<string, Innmind\Xml\Attribute>');

        new SelfClosingElement('foo', Map::of('string', 'string'));
    }

    public function testDefaultAttributes()
    {
        $node = new SelfClosingElement('foo');

        $this->assertInstanceOf(Map::class, $node->attributes());
        $this->assertSame('string', (string) $node->attributes()->keyType());
        $this->assertSame(
            Attribute::class,
            (string) $node->attributes()->valueType()
        );
    }

    public function testHasAttributes()
    {
        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
        );
        $this->assertFalse($node->hasAttributes());

        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
        );
        $this->assertTrue($node->hasAttributes());
    }

    public function testAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', $expected = new Attribute('foo'))
        );

        $this->assertSame($expected, $node->attribute('foo'));
    }

    public function testRemoveAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
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

    public function testThrowWhenRemovingUnknownAttribute()
    {
        $this->expectException(OutOfBoundsException::class);

        (new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
        ))->removeAttribute('baz');
    }

    public function testReplaceAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
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

    public function testThrowWhenReplacingUnknownAttribute()
    {
        $this->expectException(OutOfBoundsException::class);

        (new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
        ))->replaceAttribute(
            new Attribute('baz')
        );
    }

    public function testAddAttribute()
    {
        $node = new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
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

    public function testThrowWhenAttributeAlreadyExists()
    {
        $this->expectException(LogicException::class);

        (new SelfClosingElement(
            'foo',
            Map::of('string', Attribute::class)
                ('foo', new Attribute('foo'))
                ('bar', new Attribute('bar'))
        ))->addAttribute(new Attribute('foo', 'baz'));
    }

    public function testChildren()
    {
        $node = new SelfClosingElement('foo');

        $this->assertTrue(
            $node
                ->children()
                ->equals(Sequence::of(Node::class)),
        );
    }

    public function testHasChildren()
    {
        $node = new SelfClosingElement('foo');
        $this->assertFalse($node->hasChildren());
    }

    public function testThrowWhenRemovingChild()
    {
        $this->expectException(LogicException::class);

        (new SelfClosingElement('foo'))->removeChild(0);
    }

    public function testThrowWhenReplacingChild()
    {
        $this->expectException(LogicException::class);

        (new SelfClosingElement('foo'))->replaceChild(
            0,
            $this->createMock(Node::class)
        );
    }

    public function testThrowWhenPrependingChild()
    {
        $this->expectException(LogicException::class);

        (new SelfClosingElement('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    public function testThrowWhenAppendingChild()
    {
        $this->expectException(LogicException::class);

        (new SelfClosingElement('foo'))->appendChild(
            $this->createMock(Node::class)
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
            (new SelfClosingElement('foo'))->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"/>',
            (new SelfClosingElement(
                'foo',
                Map::of('string', Attribute::class)
                    ('bar', new Attribute('bar', 'baz'))
                    ('baz', new Attribute('baz', 'foo'))
            ))->toString(),
        );
    }
}
