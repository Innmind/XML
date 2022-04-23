<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Document,
    Node\Document\Version,
    Node\Document\Type,
    Node\Document\Encoding,
    Node,
    Element\Element,
    Element\SelfClosingElement,
};
use Innmind\Immutable\{
    Map,
    Sequence,
    Maybe,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class DocumentTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Document(new Version(1), Maybe::nothing(), Maybe::nothing()),
        );
    }

    public function testVersion()
    {
        $document = new Document(
            $version = new Version(1),
            Maybe::nothing(),
            Maybe::nothing(),
        );

        $this->assertSame($version, $document->version());
    }

    public function testType()
    {
        $document = new Document(new Version(1), Maybe::nothing(), Maybe::nothing());
        $this->assertFalse($document->type()->match(
            static fn() => true,
            static fn() => false,
        ));

        $document = new Document(
            new Version(1),
            Maybe::just($type = new Type('html')),
            Maybe::nothing(),
        );
        $this->assertSame($type, $document->type()->match(
            static fn($type) => $type,
            static fn() => null,
        ));
    }

    public function testDefaultChildren()
    {
        $document = new Document(new Version(1), Maybe::nothing(), Maybe::nothing());

        $this->assertInstanceOf(Sequence::class, $document->children());
    }

    public function testEncoding()
    {
        $document = new Document(new Version(1), Maybe::nothing(), Maybe::nothing());
        $this->assertFalse($document->encoding()->match(
            static fn() => true,
            static fn() => false,
        ));

        $document = new Document(
            new Version(1),
            Maybe::nothing(),
            Maybe::just($encoding = new Encoding('utf-8')),
        );
        $this->assertSame($encoding, $document->encoding()->match(
            static fn($encoding) => $encoding,
            static fn() => null,
        ));
    }

    public function testContentWithoutChildren()
    {
        $this->assertSame(
            '',
            (new Document(new Version(1), Maybe::nothing(), Maybe::nothing()))->content(),
        );
    }

    public function testContentWithChildren()
    {
        $this->assertSame(
            '<foo></foo>',
            (new Document(
                new Version(1),
                Maybe::nothing(),
                Maybe::nothing(),
                Sequence::of(new Element('foo')),
            ))->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<?xml version="2.1"?>'."\n",
            (new Document(new Version(2, 1), Maybe::nothing(), Maybe::nothing()))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n",
            (new Document(
                new Version(2, 1),
                Maybe::nothing(),
                Maybe::just(new Encoding('utf-8')),
            ))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n",
            (new Document(
                new Version(2, 1),
                Maybe::just(new Type('html')),
                Maybe::just(new Encoding('utf-8')),
            ))->toString(),
        );
        $this->assertSame(
            '<?xml version="2.1" encoding="utf-8"?>'."\n".'<!DOCTYPE html>'."\n".'<foo/>',
            (new Document(
                new Version(2, 1),
                Maybe::just(new Type('html')),
                Maybe::just(new Encoding('utf-8')),
                Sequence::of(new SelfClosingElement('foo')),
            ))->toString(),
        );
    }

    public function testPrependChild()
    {
        $document = new Document(
            new Version(1),
            Maybe::just(new Type('html')),
            Maybe::just(new Encoding('utf-8')),
            Sequence::of(
                new Element('foo'),
                new Element('bar'),
                new Element('baz'),
            ),
        );

        $document2 = $document->prependChild(
            $node = $this->createMock(Node::class),
        );

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertNotSame($document->children(), $document2->children());
        $this->assertCount(3, $document->children());
        $this->assertCount(4, $document2->children());
        $this->assertSame(
            $node,
            $document2->children()->get(0)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
        $this->assertEquals(
            $document->children()->get(0),
            $document2->children()->get(1),
        );
        $this->assertEquals(
            $document->children()->get(1),
            $document2->children()->get(2),
        );
        $this->assertEquals(
            $document->children()->get(2),
            $document2->children()->get(3),
        );
    }

    public function testAppendChild()
    {
        $document = new Document(
            new Version(1),
            Maybe::just(new Type('html')),
            Maybe::just(new Encoding('utf-8')),
            Sequence::of(
                new Element('foo'),
                new Element('bar'),
                new Element('baz'),
            ),
        );

        $document2 = $document->appendChild(
            $node = $this->createMock(Node::class),
        );

        $this->assertNotSame($document, $document2);
        $this->assertInstanceOf(Document::class, $document2);
        $this->assertSame($document->version(), $document2->version());
        $this->assertSame($document->type(), $document2->type());
        $this->assertSame($document->encoding(), $document2->encoding());
        $this->assertNotSame($document->children(), $document2->children());
        $this->assertCount(3, $document->children());
        $this->assertCount(4, $document2->children());
        $this->assertEquals(
            $document->children()->get(0),
            $document2->children()->get(0),
        );
        $this->assertEquals(
            $document->children()->get(1),
            $document2->children()->get(1),
        );
        $this->assertEquals(
            $document->children()->get(2),
            $document2->children()->get(2),
        );
        $this->assertSame(
            $node,
            $document2->children()->get(3)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(
                Set\Integers::between(0, 10),
                Set\Integers::between(0, 10),
                Set\Sequence::of(
                    Set\Decorate::immutable(
                        static fn($name) => new Element($name),
                        Set\Unicode::lengthBetween(1, 10),
                    ),
                    Set\Integers::between(0, 10),
                ),
            )
            ->then(function($major, $minor, $children) {
                $element = new Document(
                    new Version($major, $minor),
                    Maybe::nothing(),
                    Maybe::nothing(),
                    Sequence::of(...$children),
                );

                $element2 = $element->filterChild(static fn() => false);
                $element3 = $element->filterChild(static fn() => true);

                $this->assertSame($element->version(), $element2->version());
                $this->assertSame($element->version(), $element3->version());
                $this->assertTrue($element2->children()->empty());
                $this->assertTrue($element3->children()->equals($element->children()));
            });
    }

    public function testMapChild()
    {
        $this
            ->forAll(
                Set\Integers::between(0, 10),
                Set\Integers::between(0, 10),
                Set\Sequence::of(
                    Set\Decorate::immutable(
                        static fn($name) => new Element($name),
                        Set\Unicode::lengthBetween(1, 10),
                    ),
                    Set\Integers::between(1, 10),
                ),
                Set\Decorate::immutable(
                    static fn($name) => new Element($name),
                    Set\Unicode::lengthBetween(1, 10),
                ),
            )
            ->then(function($major, $minor, $children, $replacement) {
                $element = new Document(
                    new Version($major, $minor),
                    Maybe::nothing(),
                    Maybe::nothing(),
                    Sequence::of(...$children),
                );

                $element2 = $element->mapChild(static fn($child) => $replacement);

                $this->assertSame($element->version(), $element2->version());
                $this->assertFalse($element2->children()->equals($element->children()));
                $this->assertSame($element->children()->size(), $element2->children()->size());
                $this->assertTrue($element2->children()->contains($replacement));
            });
    }
}
