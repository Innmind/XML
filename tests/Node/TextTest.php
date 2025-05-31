<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Node::text('foo'),
        );
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Node::text(' foo ')->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            'foo',
            Node::text('foo')->asContent()->toString(),
        );
    }
}
