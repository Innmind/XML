<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\EntityReference,
    Node,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class EntityReferenceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new EntityReference('foo')
        );
    }

    public function testChildren()
    {
        $node = new EntityReference('foo');

        $this->assertInstanceOf(MapInterface::class, $node->children());
        $this->assertSame('int', (string) $node->children()->keyType());
        $this->assertSame(
            Node::class,
            (string) $node->children()->valueType()
        );
        $this->assertCount(0, $node->children());
        $this->assertFalse($node->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new EntityReference(' foo '))->content()
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenRemovingChild()
    {
        (new EntityReference('foo'))->removeChild(0);
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenReplacingChild()
    {
        (new EntityReference('foo'))->replaceChild(
            0,
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenPrependingChild()
    {
        (new EntityReference('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAppendingChild()
    {
        (new EntityReference('foo'))->appendChild(
            $this->createMock(Node::class)
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '&foo;',
            (string) new EntityReference('foo')
        );
    }
}
