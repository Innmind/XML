<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator;

use Innmind\Xml\{
    Translator\Translator,
    Translator\NodeTranslators,
    Translator\NodeTranslator,
    Element\Element,
    Element\SelfClosingElement,
    Node\Document,
    Node\Text,
    Node\CharacterData,
    Node\Comment,
    Exception\UnknownNodeType,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    private $translate;

    public function setUp(): void
    {
        $this->translate = new Translator(
            NodeTranslators::defaults()
        );
    }

    public function testThrowWhenInvalidTranslators()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type Map<int, Innmind\Xml\Translator\NodeTranslator>');

        new Translator(Map::of('string', 'string'));
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<foo bar="baz">
    <foobar/>
    <div>
        <![CDATA[whatever]]>
    </div>
    <!--foobaz-->
    hey!
</foo>
XML
        );
        $node = ($this->translate)($document);

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame('1.0', $node->version()->toString());
        $this->assertSame('utf-8', $node->encoding()->toString());
        $this->assertSame('html', $node->type()->name());
        $this->assertSame(
            '-//W3C//DTD HTML 4.01//EN',
            $node->type()->publicId()
        );
        $this->assertSame(
            'http://www.w3.org/TR/html4/strict.dtd',
            $node->type()->systemId()
        );
        $this->assertCount(1, $node->children());
        $foo = $node->children()->get(0);
        $this->assertInstanceOf(Element::class, $foo);
        $this->assertSame('foo', $foo->name());
        $this->assertCount(1, $foo->attributes());
        $this->assertSame('baz', $foo->attribute('bar')->value());
        $this->assertCount(7, $foo->children());
        $linebreak = $foo->children()->get(0);
        $this->assertInstanceOf(Text::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $foobar = $foo->children()->get(1);
        $this->assertInstanceOf(SelfClosingElement::class, $foobar);
        $this->assertSame('foobar', $foobar->name());
        $linebreak = $foo->children()->get(2);
        $this->assertInstanceOf(Text::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $div = $foo->children()->get(3);
        $this->assertInstanceOf(Element::class, $div);
        $this->assertSame('div', $div->name());
        $this->assertTrue($div->attributes()->empty());
        $this->assertCount(3, $div->children());
        $linebreak = $div->children()->get(0);
        $this->assertInstanceOf(Text::class, $linebreak);
        $this->assertSame("\n        ", $linebreak->content());
        $cdata = $div->children()->get(1);
        $this->assertInstanceOf(CharacterData::class, $cdata);
        $this->assertSame('whatever', $cdata->content());
        $linebreak = $div->children()->get(2);
        $this->assertInstanceOf(Text::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $linebreak = $foo->children()->get(4);
        $this->assertInstanceOf(Text::class, $linebreak);
        $this->assertSame("\n    ", $linebreak->content());
        $comment = $foo->children()->get(5);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertSame('foobaz', $comment->content());
        $text = $foo->children()->get(6);
        $this->assertInstanceOf(Text::class, $text);
        $this->assertSame("\n    hey!\n", $text->content());
        $this->assertSame($xml, $node->toString());
    }

    public function testThrowWhenNoTranslatorFoundForANodeType()
    {
        $this->expectException(UnknownNodeType::class);

        (new Translator(
            Map::of('int', NodeTranslator::class)
        ))(new \DOMDocument);
    }
}
