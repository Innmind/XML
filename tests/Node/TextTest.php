<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Text,
    Node,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class TextTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Text::of('foo'),
        );
    }

    public function testChildren()
    {
        $text = Text::of('foo');

        $this->assertInstanceOf(Sequence::class, $text->children());
        $this->assertCount(0, $text->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Text::of(' foo ')->content(),
        );
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = Text::of('foo');

        $this->assertSame(
            $node,
            $node->prependChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = Text::of('foo');

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
            'foo',
            Text::of('foo')->toString(),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $text = Text::of($data);

                $this->assertSame(
                    $text,
                    $text->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $text = Text::of($data);

                $this->assertSame(
                    $text,
                    $text->mapChild(static fn($child) => $child),
                );
            });
    }
}
