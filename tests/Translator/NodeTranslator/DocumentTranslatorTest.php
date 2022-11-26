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
};
use Innmind\Immutable\{
    Map,
    Maybe,
};
use PHPUnit\Framework\TestCase;

class DocumentTranslatorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeTranslator::class,
            DocumentTranslator::of(),
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

        $translate = DocumentTranslator::of();
        $foo = SelfClosingElement::of('foo');
        $node = $translate(
            $document,
            Translator::of(
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

        $this->assertInstanceOf(Document::class, $node);
        $this->assertSame($xml, $node->toString());
    }

    public function testReturnNothingWhenInvalidNode()
    {
        $this->assertNull(DocumentTranslator::of()(
            new \DOMNode,
            Translator::of(Map::of()),
            )->match(
                static fn($node) => $node,
                static fn() => null,
            ));
    }
}
