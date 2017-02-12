<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\CharacterDataTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Node\CharacterData
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CharacterDataTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
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

        $translator = new CharacterDataTranslator;
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

        $this->assertInstanceOf(CharacterData::class, $node);
        $this->assertSame('foo', $node->content());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new CharacterDataTranslator)->translate(
            new \DOMNode,
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
