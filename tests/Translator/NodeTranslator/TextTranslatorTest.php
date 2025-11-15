<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator,
    Node,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class TextTranslatorTest extends TestCase
{
    public function testTranslate()
    {
        $document = \Dom\XMLDocument::createFromString($xml = <<<XML
<div>foo</div>
XML
        );

        $translate = Translator::of();
        $node = $translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('foo', $node->content());
    }
}
