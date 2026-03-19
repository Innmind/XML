<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class RawTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Node::raw('foo'),
        );
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Node::raw(' foo ')->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<foo/>',
            Node::raw('<foo/>')->asContent()->toString(),
        );
    }
}
