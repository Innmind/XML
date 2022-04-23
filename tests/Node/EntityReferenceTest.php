<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\{
    Node\EntityReference,
    Node,
    Exception\LogicException,
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
            new EntityReference('foo'),
        );
    }

    public function testChildren()
    {
        $node = new EntityReference('foo');

        $this->assertInstanceOf(Sequence::class, $node->children());
        $this->assertCount(0, $node->children());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new EntityReference(' foo '))->content(),
        );
    }

    public function testThrowWhenRemovingChild()
    {
        $this->expectException(LogicException::class);

        (new EntityReference('foo'))->removeChild(0);
    }

    public function testThrowWhenReplacingChild()
    {
        $this->expectException(LogicException::class);

        (new EntityReference('foo'))->replaceChild(
            0,
            $this->createMock(Node::class),
        );
    }

    public function testThrowWhenPrependingChild()
    {
        $this->expectException(LogicException::class);

        (new EntityReference('foo'))->prependChild(
            $this->createMock(Node::class),
        );
    }

    public function testThrowWhenAppendingChild()
    {
        $this->expectException(LogicException::class);

        (new EntityReference('foo'))->appendChild(
            $this->createMock(Node::class),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '&foo;',
            (new EntityReference('foo'))->toString(),
        );
    }

    public function testFilterChild()
    {
        $this
            ->forAll(Set\Unicode::strings())
            ->then(function($data) {
                $reference = new EntityReference($data);

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
                $reference = new EntityReference($data);

                $this->assertSame(
                    $reference,
                    $reference->mapChild(static fn($child) => $child),
                );
            });
    }
}
