<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\CommentTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\Comment,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CommentTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            CommentTranslator::of(),
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div><!--foo--></div>
XML
        );

        $translate = CommentTranslator::of();
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

        $this->assertInstanceOf(Comment::class, $node);
        $this->assertSame('foo', $node->content());
    }

    public function testReturnNothingWhenInvalidNode()
    {
        $this->assertNull(CommentTranslator::of()(
            new \DOMNode,
            Translator::of(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
