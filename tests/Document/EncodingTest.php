<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Document;

use Innmind\Xml\{
    Document\Encoding,
    Exception\DomainException,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EncodingTest extends TestCase
{
    #[DataProvider('cases')]
    public function testInterface($string)
    {
        $encoding = Encoding::of($string);

        $this->assertSame($string, $encoding->toString());
    }

    #[DataProvider('invalid')]
    public function testThrowWhenInvalidName($name)
    {
        $this->expectException(DomainException::class);

        Encoding::of($name);
    }

    public static function cases(): array
    {
        return [
            ['unicode-1-1'],
            ['iso-8859-5'],
            ['Shift_JIS'],
            ['ISO_8859-9:1989'],
            ['NF_Z_62-010_(1973)'],
        ];
    }

    public static function invalid(): array
    {
        return [
            ['@'],
            ['bar+suffix'],
            ['foo/bar;q=0.8, level=1'],
        ];
    }
}
