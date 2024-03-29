<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\CharacterData,
    Node,
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
            CharacterData::of('foo'),
        );
    }

    public function testChildren()
    {
        $characterData = CharacterData::of('foo');

        $this->assertInstanceOf(Sequence::class, $characterData->children());
        $this->assertCount(0, $characterData->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            CharacterData::of(' foo ')->content(),
        );
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = CharacterData::of('foo');

        $this->assertSame(
            $node,
            $node->prependChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = CharacterData::of('foo');

        $this->assertSame(
            $node,
            $node->appendChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<![CDATA[foo]]>',
            CharacterData::of('foo')->toString(),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $characterData = CharacterData::of($data);

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
                $characterData = CharacterData::of($data);

                $this->assertSame(
                    $characterData,
                    $characterData->mapChild(static fn($child) => $child),
                );
            });
    }
}
