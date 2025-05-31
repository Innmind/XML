<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator,
    Document,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class DocumentTranslatorTest extends TestCase
{
    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<foo/>
XML
        );

        $translate = Translator::default();
        $node = $translate($document)->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame($xml, $node->toString());
    }
}
