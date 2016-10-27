<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\ElementTranslator,
    Translator\NodeTranslatorInterface,
    Translator\NodeTranslator,
    NodeInterface,
    Element\Element,
    Element\SelfClosingElement
};
use Innmind\Immutable\Map;

class ElementTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslatorInterface::class,
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

        $this->assertInstanceOf(Element::class, $node);
        $this->assertSame($xml, (string) $node);
    }

    /**
     * @expectedException Innmind\Xml\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidNode()
    {
        (new ElementTranslator)->translate(
            new \DOMNode,
            new NodeTranslator(
                new Map('int', NodeTranslatorInterface::class)
            )
        );
    }
}
