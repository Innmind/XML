<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator,
    Element,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ElementTranslatorTest extends TestCase
{
    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<bar><foo/></bar>
XML
        );

        $translate = Translator::default();
        $node = $translate($document->childNodes->item(0))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Element::class, $node);
        $this->assertSame($xml, $node->toString());
    }
}
