<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node\Document;

use Innmind\Xml\{
    Node\Document\Encoding,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class EncodingTest extends TestCase
{
    /**
     * @dataProvider cases
     */
    public function testInterface($string)
    {
        $encoding = Encoding::of($string);

        $this->assertSame($string, $encoding->toString());
    }

    /**
     * @dataProvider invalid
     */
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
