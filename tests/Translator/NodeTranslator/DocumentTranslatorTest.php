<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\DocumentTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    Node\Document,
    NodeInterface,
    Element\SelfClosingElement
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class DocumentTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
            new DocumentTranslator
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html>
<foo/>
XML
        );

        $translator = new DocumentTranslator;
        $foo = new SelfClosingElement('foo');
        $node = $translator->translate(
            $document,
            new NodeTranslator(
                (new Map('int', NodeTranslatorInterface::class))
                    ->put(
                        XML_ELEMENT_NODE,
                        new class($foo) implements NodeTranslatorInterface
                        {
                            private $foo;

                            public function __construct(NodeInterface $foo)
                            {
                                $this->foo = $foo;
                            }

                            public function translate(\DOMNode $node, NodeTranslator $translator): NodeInterface
                            {
                                return $this->foo;
                            }
                        }
                    )
            )
        );

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame($xml, (string) $node);
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new DocumentTranslator)->translate(
            new \DOMNode,
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
