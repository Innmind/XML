<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Element;

use Innmind\Xml\{
    Element,
    Element\Name,
    Node,
    Attribute,
    Format,
};
use Innmind\Immutable\{
    Map,
    Set,
    Sequence,
};
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    Set as DataSet,
};

class SelfClosingElementTest extends TestCase
{
    use BlackBox;

    public function testName()
    {
        $node = Element::selfClosing(Name::of('foo'));

        $this->assertSame('foo', $node->name()->toString());
    }

    public function testDefaultAttributes()
    {
        $node = Element::selfClosing(Name::of('foo'));

        $this->assertInstanceOf(Map::class, $node->attributes());
    }

    public function testAttribute()
    {
        $node = Element::selfClosing(
            Name::of('foo'),
            Set::of($expected = Attribute::of('foo')),
        );

        $this->assertSame($expected, $node->attribute('foo')->match(
            static fn($attribute) => $attribute,
            static fn() => null,
        ));
    }

    public function testRemoveAttribute()
    {
        $node = Element::selfClosing(
            Name::of('foo'),
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $node2 = $node->removeAttribute('foo');

        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Element::class, $node2);
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
        $element = Element::selfClosing(
            Name::of('foo'),
            Set::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $this->assertEquals($element, $element->removeAttribute('baz'));
    }

    public function testReplaceAttribute()
    {
        $node = Element::selfClosing(
            Name::of('foo'),
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
        $node = Element::selfClosing(
            Name::of('foo'),
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
        $node = Element::selfClosing(Name::of('foo'));

        $this->assertTrue(
            $node
                ->children()
                ->equals(Sequence::of()),
        );
    }

    public function testHasChildren()
    {
        $node = Element::selfClosing(Name::of('foo'));
        $this->assertTrue($node->children()->empty());
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = Element::selfClosing(Name::of('foo'));

        $this->assertSame(
            $node,
            $node->prependChild(
                Node::text(''),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = Element::selfClosing(Name::of('foo'));

        $this->assertSame(
            $node,
            $node->appendChild(
                Node::text(''),
            ),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo/>',
            Element::selfClosing(Name::of('foo'))
                ->asContent(Format::inline)
                ->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"/>',
            Element::selfClosing(
                Name::of('foo'),
                Set::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
            )
                ->asContent(Format::inline)
                ->toString(),
        );
    }

    public function testFilterChild(): BlackBox\Proof
    {
        return $this
            ->forAll(
                DataSet::strings()
                    ->madeOf(DataSet::strings()->unicode()->char())
                    ->between(1, 255)
                    ->map(Name::of(...)),
            )
            ->prove(function($name) {
                $element = Element::selfClosing($name);

                $this->assertSame(
                    $element,
                    $element->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild(): BlackBox\Proof
    {
        return $this
            ->forAll(
                DataSet::strings()
                    ->madeOf(DataSet::strings()->unicode()->char())
                    ->between(1, 255)
                    ->map(Name::of(...)),
            )
            ->prove(function($name) {
                $element = Element::selfClosing($name);

                $this->assertSame(
                    $element,
                    $element->mapChild(static fn($child) => $child),
                );
            });
    }
}
