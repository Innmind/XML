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
            new CommentTranslator
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<div><!--foo--></div>
XML
        );

        $translator = new CommentTranslator;
        $node = $translator->translate(
            $document
                ->childNodes
                ->item(0)
                ->childNodes
                ->item(0),
            new Translator(
                new Map('int', NodeTranslator::class)
            )
        );

        $this->assertInstanceOf(Comment::class, $node);
        $this->assertSame('foo', $node->content());
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new CommentTranslator)->translate(
            new \DOMNode,
            new Translator(
                new Map('int', NodeTranslator::class)
            )
        );
    }
}
