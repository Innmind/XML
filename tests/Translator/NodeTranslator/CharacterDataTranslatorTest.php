<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\CharacterDataTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\CharacterData,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CharacterDataTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            CharacterDataTranslator::of(),
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div><![CDATA[foo]]></div>
XML
        );

        $translate = CharacterDataTranslator::of();
        $node = $translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
            Translator::of(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(CharacterData::class, $node);
        $this->assertSame('foo', $node->content());
    }

    public function testReturnNothingWhenInvalidNode()
    {
        $this->assertNull(CharacterDataTranslator::of()(
            new \DOMNode,
            Translator::of(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
