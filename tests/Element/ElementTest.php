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
    Sequence,
    Monoid\Concat,
};
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    Set as DataSet,
};

class ElementTest extends TestCase
{
    use BlackBox;

    public function testName()
    {
        $node = Element::of(Name::of('foo'));

        $this->assertSame('foo', $node->name()->toString());
    }

    public function testReturnNothingWhenEmptyName()
    {
        $this->assertNull(Name::maybe('')->map(Element::of(...))->match(
            static fn($element) => $element,
            static fn() => null,
        ));
    }

    public function testDefaultAttributes()
    {
        $node = Element::of(Name::of('foo'));

        $this->assertInstanceOf(Sequence::class, $node->attributes());
    }

    public function testAttribute()
    {
        $node = Element::of(
            Name::of('foo'),
            Sequence::of($expected = Attribute::of('foo')),
        );

        $this->assertSame($expected, $node->attribute('foo')->match(
            static fn($attribute) => $attribute,
            static fn() => null,
        ));
    }

    public function testRemoveAttribute()
    {
        $node = Element::of(
            Name::of('foo'),
            Sequence::of(
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
        $this->assertSame(2, $node->attributes()->size());
        $this->assertSame(1, $node2->attributes()->size());
        $this->assertTrue($node->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertFalse($node2->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node2->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertEquals(
            $node->attribute('bar'),
            $node2->attribute('bar'),
        );
    }

    public function testDoNothingWhenRemovingUnknownAttribute()
    {
        $element = Element::of(
            Name::of('foo'),
            Sequence::of(
                Attribute::of('foo'),
                Attribute::of('bar'),
            ),
        );

        $this->assertEquals($element, $element->removeAttribute('baz'));
    }

    public function testThrowOnDuplicatedAttribute()
    {
        $this->assert()->throws(
            static fn() => Element::of(
                Name::of('foo'),
                Sequence::of(
                    Attribute::of('bar'),
                    Attribute::of('bar'),
                ),
            ),
        );
    }

    public function testReplaceAttribute()
    {
        $node = Element::of(
            Name::of('foo'),
            Sequence::of(
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
        $this->assertSame(2, $node->attributes()->size());
        $this->assertSame(2, $node2->attributes()->size());
        $this->assertTrue($node->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node2->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node2->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertEquals(
            $node->attribute('bar'),
            $node2->attribute('bar'),
        );
        $this->assertSame(
            $attribute,
            $node2->attribute('foo')->match(
                static fn($attribute) => $attribute,
                static fn() => null,
            ),
        );
    }

    public function testAddAttribute()
    {
        $node = Element::of(
            Name::of('foo'),
            Sequence::of(
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
        $this->assertSame(2, $node->attributes()->size());
        $this->assertSame(3, $node2->attributes()->size());
        $this->assertTrue($node->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node2->attribute('foo')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertTrue($node2->attribute('bar')->match(
            static fn() => true,
            static fn() => false,
        ));
        $this->assertEquals(
            $node->attribute('bar'),
            $node2->attribute('bar'),
        );
        $this->assertEquals(
            $node->attribute('foo'),
            $node2->attribute('foo'),
        );
        $this->assertSame(
            $attribute,
            $node2->attribute('baz')->match(
                static fn($attribute) => $attribute,
                static fn() => null,
            ),
        );
    }

    public function testDefaultChildren()
    {
        $node = Element::of(Name::of('foo'));

        $this->assertInstanceOf(Sequence::class, $node->children());
    }

    public function testHasChildren()
    {
        $node = Element::of(
            Name::of('foo'),
            null,
            Sequence::of(Element::of(Name::of('bar'))),
        );
        $this->assertFalse($node->children()->empty());

        $this->assertTrue(Element::of(Name::of('foo'))->children()->empty());
    }

    public function testPrependChild()
    {
        $element = Element::of(
            Name::of('foobar'),
            null,
            Sequence::of(
                Element::of(Name::of('foo')),
                Element::of(Name::of('bar')),
                Element::of(Name::of('baz')),
            ),
        );

        $element2 = $element->prependChild(
            $node = Node::text(''),
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertNotSame($element->children(), $element2->children());
        $this->assertSame(3, $element->children()->size());
        $this->assertSame(4, $element2->children()->size());
        $this->assertSame(
            $node,
            $element2->children()->get(0)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $element->children()->get(0)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
            $element2->children()->get(1)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $element->children()->get(1)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
            $element2->children()->get(2)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $element->children()->get(2)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
            $element2->children()->get(3)->match(
                static fn($value) => $value,
                static fn() => null,
            ),
        );
    }

    public function testAppendChild()
    {
        $element = Element::of(
            Name::of('foobar'),
            null,
            Sequence::of(
                Element::of(Name::of('foo')),
                Element::of(Name::of('bar')),
                Element::of(Name::of('baz')),
            ),
        );

        $element2 = $element->appendChild(
            $node = Node::text(''),
        );

        $this->assertNotSame($element, $element2);
        $this->assertInstanceOf(Element::class, $element2);
        $this->assertSame($element->name(), $element2->name());
        $this->assertSame($element->attributes(), $element2->attributes());
        $this->assertNotSame($element->children(), $element2->children());
        $this->assertSame(3, $element->children()->size());
        $this->assertSame(4, $element2->children()->size());
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

    public function testCast()
    {
        $this->assertSame(
            '<foo></foo>',
            Element::of(Name::of('foo'))
                ->asContent(Format::inline)
                ->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"></foo>',
            Element::of(
                Name::of('foo'),
                Sequence::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
            )
                ->asContent(Format::inline)
                ->toString(),
        );
        $this->assertSame(
            '<foo bar="baz" baz="foo"><bar></bar><baz></baz></foo>',
            Element::of(
                Name::of('foo'),
                Sequence::of(
                    Attribute::of('bar', 'baz'),
                    Attribute::of('baz', 'foo'),
                ),
                Sequence::of(
                    Element::of(Name::of('bar')),
                    Element::of(Name::of('baz')),
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
                DataSet::sequence(
                    DataSet::strings()
                        ->madeOf(DataSet::strings()->unicode()->char())
                        ->between(1, 255)
                        ->map(Name::of(...))
                        ->map(Element::of(...)),
                )->between(0, 10),
            )
            ->prove(function($name, $children) {
                $element = Element::of(
                    $name,
                    null,
                    Sequence::of(...$children),
                );

                $element2 = $element->filterChild(static fn() => false);
                $element3 = $element->filterChild(static fn() => true);

                $this->assertSame($name, $element2->name());
                $this->assertSame($name, $element3->name());
                $this->assertTrue($element2->children()->empty());
                $this->assertTrue($element3->children()->equals($element->children()));
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
                DataSet::sequence(
                    DataSet::strings()
                        ->madeOf(DataSet::strings()->unicode()->char())
                        ->between(1, 10)
                        ->map(Name::of(...))
                        ->map(Element::of(...)),
                )->between(1, 10),
                DataSet::strings()
                    ->madeOf(DataSet::strings()->unicode()->char())
                    ->between(1, 10)
                    ->map(Name::of(...))
                    ->map(Element::of(...)),
            )
            ->prove(function($name, $children, $replacement) {
                $element = Element::of(
                    $name,
                    null,
                    Sequence::of(...$children),
                );

                $element2 = $element->mapChild(static fn($child) => $replacement);

                $this->assertSame($name, $element2->name());
                $this->assertFalse($element2->children()->equals($element->children()));
                $this->assertSame($element->children()->size(), $element2->children()->size());
                $this->assertTrue($element2->children()->contains($replacement));
            });
    }

    public function testAsContent()
    {
        $element = Element::of(
            Name::of('foo'),
            Sequence::of(
                Attribute::of('bar', 'baz'),
                Attribute::of('baz', 'foo'),
            ),
            Sequence::of(
                Element::of(Name::of('bar')),
                Element::of(Name::of('baz')),
            ),
        );

        $this->assertSame(
            <<<CONTENT
            <foo bar="baz" baz="foo">
                <bar></bar>
                <baz></baz>
            </foo>

            CONTENT,
            $element->asContent()->toString(),
        );
    }

    public function testAsContentWritesOnSingleLineWhenContainingASingleNode()
    {
        $element = Element::of(
            Name::of('foo'),
            null,
            Sequence::of(
                Node::text('bar'),
            ),
        );

        $this->assertSame(
            "<foo>bar</foo>\n",
            $element->asContent()->toString(),
        );
    }

    public function testAsContentRenderingIsLazy()
    {
        $loaded = false;
        $element = Element::of(
            Name::of('foo'),
            null,
            Sequence::lazy(static function() use (&$loaded) {
                yield Element::of(Name::of('bar'));
                $loaded = true;
                yield Element::of(Name::of('baz'));
            }),
        );

        $this->assertSame(
            '<foo>    <bar></bar>',
            $element
                ->asContent()
                ->lines()
                ->take(2)
                ->map(static fn($line) => $line->str())
                ->fold(new Concat)
                ->toString(),
        );
        $this->assertFalse($loaded);
    }

    public function testAsContentWithNamespacedName()
    {
        $this->assertSame(
            '<xades:IssuerName></xades:IssuerName>',
            Element::of(Name::namespaced('xades', 'IssuerName'))
                ->asContent(Format::inline)
                ->toString(),
        );
    }

    public function testAsContentWithNamespacedAttribute()
    {
        $this->assertSame(
            '<xades:IssuerName xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"></xades:IssuerName>',
            Element::of(
                Name::namespaced('xades', 'IssuerName'),
                Sequence::of(
                    Attribute::namespaced(
                        'xmlns',
                        'xades',
                        'http://uri.etsi.org/01903/v1.3.2#',
                    ),
                ),
            )
                ->asContent(Format::inline)
                ->toString(),
        );
    }
}
