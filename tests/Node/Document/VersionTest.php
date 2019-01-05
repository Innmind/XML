<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node\Document;

use Innmind\Xml\{
    Node\Document\Version,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class VersionTest extends TestCase
{
    public function testInterface()
    {
        $version = new Version(2, 1);

        $this->assertSame(2, $version->major());
        $this->assertSame(1, $version->minor());
        $this->assertSame('2.1', (string) $version);

        $this->assertSame('1.0', (string) new Version(1));
    }

    public function testThrowWhenMajorTooLow()
    {
        $this->expectException(DomainException::class);

        new Version(-1);
    }

    public function testThrowWhenMinorTooLow()
    {
        $this->expectException(DomainException::class);

        new Version(1, -1);
    }
}
