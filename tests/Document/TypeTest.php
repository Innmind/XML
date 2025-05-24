<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Document;

use Innmind\Xml\{
    Document\Type,
    Exception\DomainException,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TypeTest extends TestCase
{
    #[DataProvider('cases')]
    public function testInterface($name, $public, $system, $string)
    {
        $type = Type::of($name, $public, $system);

        $this->assertSame($name, $type->name());
        $this->assertSame($public, $type->publicId());
        $this->assertSame($system, $type->systemId());
        $this->assertSame($string, $type->toString());
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(DomainException::class);

        Type::of('');
    }

    public static function cases(): array
    {
        return [
            ['foo', '', '', '<!DOCTYPE foo>'],
            ['foo', 'bar', '', '<!DOCTYPE foo PUBLIC "bar">'],
            ['foo', 'bar', 'baz', '<!DOCTYPE foo PUBLIC "bar" "baz">'],
            ['foo', '', 'baz', '<!DOCTYPE foo "baz">'],
        ];
    }
}
