<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\CharacterData,
    Node,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class CharacterDataTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new CharacterData('foo')
        );
    }

    public function testChildren()
    {
        $characterData = new CharacterData('foo');

        $this->assertInstanceOf(MapInterface::class, $characterData->children());
        $this->assertSame('int', (string) $characterData->children()->keyType());
        $this->assertSame(
            Node::class,
            (string) $characterData->children()->ValueType()
        );
        $this->assertCount(0, $characterData->children());
        $this->assertFalse($characterData->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new CharacterData(' foo '))->content()
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenRemovingChild()
    {
        (new CharacterData('foo'))->removeChild(0);
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenReplacingChild()
    {
        (new CharacterData('foo'))->replaceChild(
            0,
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenPrependingChild()
    {
        (new CharacterData('foo'))->prependChild(
            $this->createMock(Node::class)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\LogicException
     */
    public function testThrowWhenAppendingChild()
    {
        (new CharacterData('foo'))->appendChild(
            $this->createMock(Node::class)
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<![CDATA[foo]]>',
            (string) new CharacterData('foo')
        );
    }
}
