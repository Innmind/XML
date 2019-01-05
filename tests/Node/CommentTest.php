<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Comment,
    Node,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Comment('foo')
        );
    }

    public function testChildren()
    {
        $comment = new Comment('foo');

        $this->assertInstanceOf(MapInterface::class, $comment->children());
        $this->assertSame('int', (string) $comment->children()->keyType());
        $this->assertSame(
            Node::class,
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
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenPrependingChild()
    {
        (new Comment('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAppendingChild()
    {
        (new Comment('foo'))->appendChild(
            $this->createMock(Node::class)
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
