<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator\DocumentTranslator,
    Translator\NodeTranslator,
    Translator\Translator,
    Node\Document,
    Node,
    Element\SelfClosingElement,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class DocumentTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            new DocumentTranslator,
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

        $translate = new DocumentTranslator;
        $foo = new SelfClosingElement('foo');
        $node = $translate(
            $document,
            new Translator(
                Map::of([
                    \XML_ELEMENT_NODE,
                    new class($foo) implements NodeTranslator {
                        private $foo;

                        public function __construct(Node $foo)
                        {
                            $this->foo = $foo;
                        }

                        public function __invoke(\DOMNode $node, Translator $translate): Node
                        {
                            return $this->foo;
                        }
                    },
                ]),
            ),
        );

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame($xml, $node->toString());
    }

    public function testThrowWhenInvalidNode()
    {
        $this->expectException(InvalidArgumentException::class);

        (new DocumentTranslator)(
            new \DOMNode,
            new Translator(Map::of()),
        );
    }
}
