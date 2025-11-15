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
        $document = \Dom\XMLDocument::createFromString('<root></root>');

        $children = Translator::of()(
            $document->childNodes->item(0),
        )->match(
            static fn($element) => $element->children(),
            static fn() => null,
        );

        $this->assertInstanceOf(Sequence::class, $children);
        $this->assertSame(0, $children->size());
    }

    public function testChildren()
    {
        $document = \Dom\XMLDocument::createFromString('<root><foo/><bar/></root>');

        $children = Translator::of()(
            $document->childNodes->item(0),
        )->match(
            static fn($element) => $element->children(),
            static fn() => null,
        );

        $this->assertInstanceOf(Sequence::class, $children);
        $this->assertSame(2, $children->size());
    }
}
