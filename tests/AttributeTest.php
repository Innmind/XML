<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\{
    Attribute,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        new Attribute('');
    }

    public function testEmptyValue()
    {
        $attribute = new Attribute('foo');

        $this->assertSame('foo', $attribute->name());
        $this->assertSame('', $attribute->value());
    }

    public function testWithValue()
    {
        $attribute = new Attribute('foo', 'bar');

        $this->assertSame('foo', $attribute->name());
        $this->assertSame('bar', $attribute->value());
    }

    public function testCastWithNoValue()
    {
        $this->assertSame(
            'foo',
            (new Attribute('foo'))->toString(),
        );
    }

    public function testCastWithValue()
    {
        $this->assertSame(
            'foo="bar"',
            (new Attribute('foo', 'bar'))->toString(),
        );
    }
}
