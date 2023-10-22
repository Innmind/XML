<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\ParentNode,
    Reader\Reader,
    Element\Element,
};
use Innmind\Filesystem\File\Content;
use PHPUnit\Framework\TestCase;

class ParentNodeTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = Reader::of();
    }

    public function testInterface()
    {
        $xml = <<<XML
<div><div><foo /><bar /></div></div>
XML;
        $tree = ($this->read)(
            Content::ofString($xml),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $parent = $tree
            ->children()
            ->get(0)
            ->map(static fn($node) => $node->children())
            ->flatMap(static fn($children) => $children->get(0))
            ->match(
                static fn($parent) => $parent,
                static fn() => null,
            );
        $node = $parent
            ->children()
            ->get(1)
            ->match(
                static fn($node) => $node,
                static fn() => null,
            );

        $this->assertSame(
            $parent,
            ParentNode::of($node)($tree)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
    }

    public function testReturnNothingWhenNoParentFound()
    {
        $this->assertNull(ParentNode::of(Element::of('foo'))(Element::of('bar'))->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
