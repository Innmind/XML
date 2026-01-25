<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator,
    Element,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class EntityReferenceTranslatorTest extends TestCase
{
    public function testTranslate()
    {
        $document = \Dom\XMLDocument::createFromString($xml = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <foo>&gt;</foo>
            XML
        );

        $translate = Translator::of();
        $node = $translate(
            $document->childNodes->item(0),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Element::class, $node);
        $this->assertSame("<foo>&gt;</foo>\n", $node->asContent()->toString());
    }
}
