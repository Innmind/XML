<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\CharacterData,
    Node,
    Exception\LogicException,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;

class CharacterDataTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            new CharacterData('foo'),
        );
    }

    public function testChildren()
    {
        $characterData = new CharacterData('foo');

        $this->assertInstanceOf(Sequence::class, $characterData->children());
        $this->assertCount(0, $characterData->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new CharacterData(' foo '))->content(),
        );
    }

    public function testThrowWhenRemovingChild()
    {
        $this->expectException(LogicException::class);

        (new CharacterData('foo'))->removeChild(0);
    }

    public function testThrowWhenReplacingChild()
    {
        $this->expectException(LogicException::class);

        (new CharacterData('foo'))->replaceChild(
            0,
            $this->createMock(Node::class),
        );
    }

    public function testThrowWhenPrependingChild()
    {
        $this->expectException(LogicException::class);

        (new CharacterData('foo'))->prependChild(
            $this->createMock(Node::class),
        );
    }

    public function testThrowWhenAppendingChild()
    {
        $this->expectException(LogicException::class);

        (new CharacterData('foo'))->appendChild(
            $this->createMock(Node::class),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<![CDATA[foo]]>',
            (new CharacterData('foo'))->toString(),
        );
    }
}
