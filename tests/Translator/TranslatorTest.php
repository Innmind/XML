<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator;

use Innmind\Xml\{
    Translator,
    Element,
    Element\Custom,
    Node,
    Document,
    Format,
};
use Innmind\Immutable\Maybe;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    private $translate;

    public function setUp(): void
    {
        $this->translate = Translator::of();
    }

    public function testTranslate()
    {
        $xml = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <foo bar="baz">
            <foobar/>
            <div>
                <![CDATA[whatever]]>
            </div>
            <!--foobaz-->
            hey!
        </foo>
        XML;

        $document = \Dom\XMLDocument::createFromString($xml);

        $node = ($this->translate)($document)->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame('1.0', $node->version()->toString());
        $this->assertSame('utf-8', $node->encoding()->match(
            static fn($encoding) => $encoding->toString(),
            static fn() => null,
        ));
        $this->assertSame('html', $node->type()->match(
            static fn($type) => $type->name(),
            static fn() => null,
        ));
        $this->assertSame(
            '-//W3C//DTD HTML 4.01//EN',
            $node->type()->match(
                static fn($type) => $type->publicId(),
                static fn() => null,
            ),
        );
        $this->assertSame(
            'http://www.w3.org/TR/html4/strict.dtd',
            $node->type()->match(
                static fn($type) => $type->systemId(),
                static fn() => null,
            ),
        );
        $this->assertSame(1, $node->children()->size());
        $foo = $node->children()->get(0)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Element::class, $foo);
        $this->assertSame('foo', $foo->name()->toString());
        $this->assertSame(1, $foo->attributes()->size());
        $this->assertSame('baz', $foo->attribute('bar')->match(
            static fn($attribute) => $attribute->value(),
            static fn() => null,
        ));
        $this->assertSame(7, $foo->children()->size());
        $linebreak = $foo->children()->get(0)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $foobar = $foo->children()->get(1)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Element::class, $foobar);
        $this->assertSame('foobar', $foobar->name()->toString());
        $this->assertSame('<foobar/>', $foobar->asContent(Format::inline)->toString());
        $linebreak = $foo->children()->get(2)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $div = $foo->children()->get(3)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Element::class, $div);
        $this->assertSame('div', $div->name()->toString());
        $this->assertTrue($div->attributes()->empty());
        $this->assertSame(3, $div->children()->size());
        $linebreak = $div->children()->get(0)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $linebreak);
        $this->assertSame("\n        ", $linebreak->content());
        $cdata = $div->children()->get(1)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $cdata);
        $this->assertSame('whatever', $cdata->content());
        $linebreak = $div->children()->get(2)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $linebreak = $foo->children()->get(4)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $comment = $foo->children()->get(5)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $comment);
        $this->assertSame('foobaz', $comment->content());
        $text = $foo->children()->get(6)->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $this->assertInstanceOf(Node::class, $text);
        $this->assertSame("\n    hey!\n", $text->content());
        $this->assertSame($xml, $node->asContent(Format::inline)->toString());
    }

    public function testAllowToTranslateCustomElements()
    {
        $custom = new class implements Custom {
            public function normalize(): Element
            {
            }
        };
        $translate = Translator::of(static fn() => Maybe::just($custom));
        $document = \Dom\XMLDocument::createFromString('<foo/>');

        $this->assertSame(
            $custom,
            $translate($document)
                ->maybe()
                ->flatMap(static fn($document) => $document->children()->first())
                ->match(
                    static fn($element) => $element,
                    static fn() => null,
                ),
        );
    }
}
