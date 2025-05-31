<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\Attribute;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

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
}
