<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element\SelfClosingElement,
    Node,
    Attribute,
    Exception\DomainException,
};
use Innmind\Immutable\{
    Map,
    Set,
    Sequence,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set as DataSet,
};

class SelfClosingElementTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            SelfClosingElement::of('foo'),
        );
    }

    public function testName()
    {
        $node = SelfClosingElement::of('foo');

        $this->assertSame('foo', $node->name());
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        SelfClosingElement::of('');
    }

    public function testDefaultAttributes()
    {
        $node = SelfClosingElement::of('foo');

        $this->assertInstanceOf(Map::class, $node->attributes());
    }

    public function testAttribute()
    {
        $node = SelfClosingElement::of(
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
        $node = SelfClosingElement::of(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $node2 = $node->removeAttribute('foo');

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertTrue($node2->children()->empty());
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
        $element = SelfClosingElement::of(
            'foo',
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $this->assertEquals($element, $element->removeAttribute('baz'));
    }

    public function testReplaceAttribute()
    {
        $node = SelfClosingElement::of(
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
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertTrue($node2->children()->empty());
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
        $node = SelfClosingElement::of(
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
        $this->assertInstanceOf(SelfClosingElement::class, $node2);
        $this->assertSame($node->name(), $node2->name());
        $this->assertTrue($node2->children()->empty());
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

    public function testChildren()
    {
        $node = SelfClosingElement::of('foo');

        $this->assertTrue(
            $node
                ->children()
                ->equals(Sequence::of()),
        );
    }

    public function testHasChildren()
    {
        $node = SelfClosingElement::of('foo');
        $this->assertTrue($node->children()->empty());
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = SelfClosingElement::of('foo');

        $this->assertSame(
            $node,
            $node->prependChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = SelfClosingElement::of('foo');

        $this->assertSame(
            $node,
            $node->appendChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testContent()
    {
        $this->assertSame(
            '',
            SelfClosingElement::of('foo')->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo/>',
            SelfClosingElement::of('foo')->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"/>',
            SelfClosingElement::of(
                'foo',
                Set::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
            )->toString(),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(DataSet\Strings::madeOf(DataSet\Unicode::any())->between(1, 255))
            ->then(function($name) {
                $element = SelfClosingElement::of($name);

                $this->assertSame(
                    $element,
                    $element->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild()
    {
        $this
            ->forAll(DataSet\Strings::madeOf(DataSet\Unicode::any())->between(1, 255))
            ->then(function($name) {
                $element = SelfClosingElement::of($name);

                $this->assertSame(
                    $element,
                    $element->mapChild(static fn($child) => $child),
                );
            });
    }
}
