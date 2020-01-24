<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\CharacterDataTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\CharacterData,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CharacterDataTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new CharacterDataTranslator
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div><![CDATA[foo]]></div>
XML
        );

        $translate = new CharacterDataTranslator;
        $node = $translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
            new Translator(
                Map::of('int', NodeTranslator::class)
            )
        );

        $this->assertInstanceOf(CharacterData::class, $node);
        $this->assertSame('foo', $node->content());
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new CharacterDataTranslator)(
            new \DOMNode,
            new Translator(
                Map::of('int', NodeTranslator::class)
            )
        );
    }
}
