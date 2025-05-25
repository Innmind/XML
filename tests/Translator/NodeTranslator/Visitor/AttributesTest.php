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
        $document = new \DOMDocument;
        $document->loadXML('<foo/>');

        $attributes = Translator::default()($document->childNodes->item(0))->match(
            static fn($element) => $element->attributes()->values()->toSet(),
            static fn() => null,
        );

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertCount(0, $attributes);
    }

    public function testAttributes()
    {
        $document = new \DOMDocument;
        $document->loadXML('<hr bar="baz" foobar=""/>');

        $attributes = Translator::default()($document->childNodes->item(0))->match(
            static fn($element) => $element->attributes()->values()->toSet(),
            static fn() => null,
        );

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertCount(2, $attributes);
        $attributes = $attributes->toList();
        $this->assertSame('bar', $attributes[0]->name());
        $this->assertSame('baz', $attributes[0]->value());
        $this->assertSame('foobar', $attributes[1]->name());
        $this->assertSame('', $attributes[1]->value());
    }
}
