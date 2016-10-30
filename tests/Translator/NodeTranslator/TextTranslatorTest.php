<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\TextTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Node\Text
};
use Innmind\Immutable\Map;

class TextTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
            new TextTranslator
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div>foo</div>
XML
        );

        $translator = new TextTranslator;
        $node = $translator->translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );

        $this->assertInstanceOf(Text::class, $node);
        $this->assertSame('foo', $node->content());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new TextTranslator)->translate(
            new \DOMNode,
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
