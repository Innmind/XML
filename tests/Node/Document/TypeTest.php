<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node\Document;

use Innmind\Xml\{
    Node\Document\Type,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @dataProvider cases
     */
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
