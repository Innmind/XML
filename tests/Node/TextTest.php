<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Text,
    Node,
};
use Innmind\Immutable\MapInterface;
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

        $this->assertInstanceOf(MapInterface::class, $text->children());
        $this->assertSame('int', (string) $text->children()->keyType());
        $this->assertSame(
            Node::class,
            (string) $text->children()->valueType()
        );
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

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenRemovingChild()
    {
        (new Text('foo'))->removeChild(0);
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenReplacingChild()
    {
        (new Text('foo'))->replaceChild(
            0,
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenPrependingChild()
    {
        (new Text('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAppendingChild()
    {
        (new Text('foo'))->appendChild(
            $this->createMock(Node::class)
        );
    }

    public function testCast()
    {
        $this->assertSame(
            'foo',
            (string) new Text('foo')
        );
    }
}
