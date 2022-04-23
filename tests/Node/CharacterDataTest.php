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
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class CharacterDataTest extends TestCase
{
    use BlackBox;

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

    public function testFilterChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $characterData = new CharacterData($data);

                $this->assertSame(
                    $characterData,
                    $characterData->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $characterData = new CharacterData($data);

                $this->assertSame(
                    $characterData,
                    $characterData->mapChild(static fn($child) => $child),
                );
            });
    }
}
