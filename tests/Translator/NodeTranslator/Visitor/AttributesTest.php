<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\Translator;
use Innmind\Immutable\Set;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testNoAttributes()
    {
        $document = \Dom\XMLDocument::createFromString('<foo/>');

        $attributes = Translator::of()($document->childNodes->item(0))->match(
            static fn($element) => $element->attributes()->toSet(),
            static fn() => null,
        );

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertSame(0, $attributes->size());
    }

    public function testAttributes()
    {
        $document = \Dom\XMLDocument::createFromString('<hr bar="baz" foobar=""/>');

        $attributes = Translator::of()($document->childNodes->item(0))->match(
            static fn($element) => $element->attributes()->toSet(),
            static fn() => null,
        );

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertSame(2, $attributes->size());
        $attributes = $attributes->toList();
        $this->assertSame('bar', $attributes[0]->name());
        $this->assertSame('baz', $attributes[0]->value());
        $this->assertSame('foobar', $attributes[1]->name());
        $this->assertSame('', $attributes[1]->value());
    }
}
