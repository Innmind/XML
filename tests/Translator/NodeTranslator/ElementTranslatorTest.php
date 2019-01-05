<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\ElementTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Element\Element,
    Element\SelfClosingElement,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ElementTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new ElementTranslator
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<bar><foo/></bar>
XML
        );

        $translator = new ElementTranslator;
        $foo = new SelfClosingElement('foo');
        $node = $translator->translate(
            $document->childNodes->item(0),
            new Translator(
                Map::of('int', NodeTranslator::class)
                    (
                        XML_ELEMENT_NODE,
                        new class($foo) implements NodeTranslator
                        {
                            private $foo;

                            public function __construct(Node $foo)
                            {
                                $this->foo = $foo;
                            }

                            public function translate(\DOMNode $node, Translator $translator): Node
                            {
                                return $this->foo;
                            }
                        }
                    )
            )
        );

        $this->assertInstanceOf(Element::class, $node);
        $this->assertSame($xml, (string) $node);
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new ElementTranslator)->translate(
            new \DOMNode,
            new Translator(
                new Map('int', NodeTranslator::class)
            )
        );
    }
}
