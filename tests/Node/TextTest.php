<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Text,
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new Text('foo')
        );
    }

    public function testChildren()
    {
        $text = new Text('foo');

        $this->assertInstanceOf(Sequence::class, $text->children());
        $this->assertSame(Node::class, $text->children()->type());
        $this->assertCount(0, $text->children());
        $this->assertFalse($text->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new Text(' foo '))->content()
        );
    }

    public function testThrowWhenRemovingChild()
    {
        $this->expectException(LogicException::class);

        (new Text('foo'))->removeChild(0);
    }

    public function testThrowWhenReplacingChild()
    {
        $this->expectException(LogicException::class);

        (new Text('foo'))->replaceChild(
            0,
            $this->createMock(Node::class)
        );
    }

    public function testThrowWhenPrependingChild()
    {
        $this->expectException(LogicException::class);

        (new Text('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    public function testThrowWhenAppendingChild()
    {
        $this->expectException(LogicException::class);

        (new Text('foo'))->appendChild(
            $this->createMock(Node::class)
        );
    }

    public function testCast()
    {
        $this->assertSame(
            'foo',
            (new Text('foo'))->toString(),
        );
    }
}
