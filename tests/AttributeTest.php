<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testReturnNothingWhenEmptyName()
    {
        $this->assertNull(Attribute::maybe('')->match(
            static fn($attribute) => $attribute,
            static fn() => null,
        ));
    }

    public function testEmptyValue()
    {
        $attribute = Attribute::of('foo');

        $this->assertSame('foo', $attribute->name());
        $this->assertSame('', $attribute->value());
    }

    public function testWithValue()
    {
        $attribute = Attribute::of('foo', 'bar');

        $this->assertSame('foo', $attribute->name());
        $this->assertSame('bar', $attribute->value());
    }

    public function testCastWithNoValue()
    {
        $this->assertSame(
            'foo',
            Attribute::of('foo')->toString(),
        );
    }

    public function testCastEmptyAttribute()
    {
        $this->assertSame(
            'foo=""',
            Attribute::empty('foo')->toString(),
        );
    }

    public function testCastWithValue()
    {
        $this->assertSame(
            'foo="bar"',
            Attribute::of('foo', 'bar')->toString(),
        );
    }
}
