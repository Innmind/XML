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
        $version = Version::of(2, 1);

        $this->assertSame(2, $version->major());
        $this->assertSame(1, $version->minor());
        $this->assertSame('2.1', $version->toString());

        $this->assertSame('1.0', Version::of(1)->toString());
    }

    public function testThrowWhenMajorTooLow()
    {
        $this->expectException(DomainException::class);

        Version::of(-1);
    }

    public function testThrowWhenMinorTooLow()
    {
        $this->expectException(DomainException::class);

        Version::of(1, -1);
    }
}
