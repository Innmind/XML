<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\NodeTranslator\Visitor\Attributes,
    Attribute,
};
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testSimpleNode()
    {
        $attributes = (new Attributes)(new \DOMNode);

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertSame(Attribute::class, $attributes->type());
        $this->assertCount(0, $attributes);
    }

    public function testNoAttributes()
    {
        $document = new \DOMDocument;
        $document->loadXML('<foo/>');

        $attributes = (new Attributes)($document->childNodes->item(0));

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertSame(Attribute::class, $attributes->type());
        $this->assertCount(0, $attributes);
    }

    public function testAttributes()
    {
        $document = new \DOMDocument;
        $document->loadXML('<hr bar="baz" foobar=""/>');

        $attributes = (new Attributes)($document->childNodes->item(0));

        $this->assertInstanceOf(Set::class, $attributes);
        $this->assertSame(Attribute::class, $attributes->type());
        $this->assertCount(2, $attributes);
        $attributes = unwrap($attributes);
        $this->assertSame('bar', $attributes[0]->name());
        $this->assertSame('baz', $attributes[0]->value());
        $this->assertSame('foobar', $attributes[1]->name());
        $this->assertSame('', $attributes[1]->value());
    }
}
