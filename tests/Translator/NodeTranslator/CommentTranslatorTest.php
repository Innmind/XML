<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\CommentTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Node\Comment
};
use Innmind\Immutable\Map;

class CommentTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
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
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
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
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
