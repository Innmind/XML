<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\Comment,
    Node,
};
use Innmind\Immutable\Sequence;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    Set,
};

class CommentTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Comment::of('foo'),
        );
    }

    public function testChildren()
    {
        $comment = Comment::of('foo');

        $this->assertInstanceOf(Sequence::class, $comment->children());
        $this->assertCount(0, $comment->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Comment::of(' foo ')->content(),
        );
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = Comment::of('foo');

        $this->assertSame(
            $node,
            $node->prependChild(
                Node\Text::of(''),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = Comment::of('foo');

        $this->assertSame(
            $node,
            $node->appendChild(
                Node\Text::of(''),
            ),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<!--foo-->',
            Comment::of('foo')->toString(),
        );
    }

    public function testFilterChild(): BlackBox\Proof
    {
        return $this
            ->forAll(Set::strings()->unicode())
            ->prove(function($data) {
                $comment = Comment::of($data);

                $this->assertSame(
                    $comment,
                    $comment->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild(): BlackBox\Proof
    {
        return $this
            ->forAll(Set::strings()->unicode())
            ->prove(function($data) {
                $comment = Comment::of($data);

                $this->assertSame(
                    $comment,
                    $comment->mapChild(static fn($child) => $child),
                );
            });
    }
}
