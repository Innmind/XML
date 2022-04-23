<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\EntityReference,
    Node,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class EntityReferenceTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            EntityReference::of('foo'),
        );
    }

    public function testChildren()
    {
        $node = EntityReference::of('foo');

        $this->assertInstanceOf(Sequence::class, $node->children());
        $this->assertCount(0, $node->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            EntityReference::of(' foo ')->content(),
        );
    }

    public function testDoNothingWhenPrependingChild()
    {
        $node = EntityReference::of('foo');

        $this->assertSame(
            $node,
            $node->prependChild(
                $this->createMock(Node::class),
            ),
        );
    }

    public function testDoNothingWhenAppendingChild()
    {
        $node = EntityReference::of('foo');

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
            '&foo;',
            EntityReference::of('foo')->toString(),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $reference = EntityReference::of($data);

                $this->assertSame(
                    $reference,
                    $reference->filterChild(static fn() => true),
                );
            });
    }

    public function testMapChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $reference = EntityReference::of($data);

                $this->assertSame(
                    $reference,
                    $reference->mapChild(static fn($child) => $child),
                );
            });
    }
}
