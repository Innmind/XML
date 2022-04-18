<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\TextTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\Text,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class TextTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new TextTranslator,
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div>foo</div>
XML
        );

        $translate = new TextTranslator;
        $node = $translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
            new Translator(
                Map::of('int', NodeTranslator::class),
            ),
        );

        $this->assertInstanceOf(Text::class, $node);
        $this->assertSame('foo', $node->content());
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new TextTranslator)(
            new \DOMNode,
            new Translator(
                Map::of('int', NodeTranslator::class),
            )
        );
    }
}
