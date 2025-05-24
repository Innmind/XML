<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Node::comment('foo'),
        );
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Node::comment(' foo ')->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<!--foo-->',
            Node::comment('foo')->toString(),
        );
    }
}
