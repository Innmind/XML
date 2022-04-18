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
};
use Innmind\Immutable\{
    Map,
    Maybe,
};
use PHPUnit\Framework\TestCase;

class ElementTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new ElementTranslator,
        );
    }

    public function testTranslate()
    {
        $document = new \DOMDocument;
        $document->loadXML($xml = <<<XML
<bar><foo/></bar>
XML
        );

        $translate = new ElementTranslator;
        $foo = new SelfClosingElement('foo');
        $node = $translate(
            $document->childNodes->item(0),
            new Translator(
                Map::of([
                    \XML_ELEMENT_NODE,
                    new class($foo) implements NodeTranslator {
                        private $foo;

                        public function __construct(Node $foo)
                        {
                            $this->foo = $foo;
                        }

                        public function __invoke(\DOMNode $node, Translator $translate): Maybe
                        {
                            return Maybe::just($this->foo);
                        }
                    },
                ]),
            ),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Element::class, $node);
        $this->assertSame($xml, $node->toString());
    }

    public function testReturnNothingWhenInvalidNode()
    {
        $this->assertNull((new ElementTranslator)(
            new \DOMNode,
            new Translator(Map::of()),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
