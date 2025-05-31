<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Document;

use Innmind\Xml\Document\Encoding;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    Set,
};
use PHPUnit\Framework\Attributes\DataProvider;

class EncodingTest extends TestCase
{
    use BlackBox;

    #[DataProvider('cases')]
    public function testInterface($string)
    {
        $encoding = Encoding::of($string)->match(
            static fn($encoding) => $encoding,
            static fn() => null,
        );

        $this->assertNotNull($encoding);
        $this->assertSame($string, $encoding->toString());
    }

    public function testThrowWhenInvalidName(): BlackBox\Proof
    {
        return $this
            ->forAll(Set::strings())
            ->prove(function($random) {
                $this->assertNull(Encoding::of($random)->match(
                    static fn($encoding) => $encoding,
                    static fn() => null,
                ));
            });
    }

    public static function cases(): array
    {
        return [
            ['utf-8'],
            ['us-ascii'],
        ];
    }
}
