<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Translator;
use Innmind\Immutable\Sequence;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ChildrenTest extends TestCase
{
    public function testNoChildren()
    {
        $document = new \DOMDocument;
        $document->loadXML('<root></root>');

        $children = Translator::of()(
            $document->childNodes->item(0),
        )->match(
            static fn($element) => $element->children(),
            static fn() => null,
        );

        $this->assertInstanceOf(Sequence::class, $children);
        $this->assertCount(0, $children);
    }

    public function testChildren()
    {
        $document = new \DOMDocument;
        $document->loadXML('<root><foo/><bar/></root>');

        $children = Translator::of()(
            $document->childNodes->item(0),
        )->match(
            static fn($element) => $element->children(),
            static fn() => null,
        );

        $this->assertInstanceOf(Sequence::class, $children);
        $this->assertCount(2, $children);
    }
}
