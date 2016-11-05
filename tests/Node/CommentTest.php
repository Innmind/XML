<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
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

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenRemovingChild()
    {
        (new Comment('foo'))->removeChild(0);
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenReplacingChild()
    {
        (new Comment('foo'))->replaceChild(
            0,
            $this->createMock(NodeInterface::class)
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
