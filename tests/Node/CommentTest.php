<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Node;

use Innmind\XML\{
    Node\Comment,
    NodeInterface
};
use Innmind\Immutable\MapInterface;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new Comment('foo')
        );
    }

    public function testChildren()
    {
        $comment = new Comment('foo');

        $this->assertInstanceOf(MapInterface::class, $comment->children());
        $this->assertSame('int', (string) $comment->children()->keyType());
        $this->assertSame(
            NodeInterface::class,
            (string) $comment->children()->ValueType()
        );
        $this->assertCount(0, $comment->children());
        $this->assertFalse($comment->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new Comment(' foo '))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<!--foo-->',
            (string) new Comment('foo')
        );
    }
}
