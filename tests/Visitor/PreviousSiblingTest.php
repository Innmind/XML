<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\PreviousSibling,
    Reader\Reader,
};
use Innmind\Filesystem\File\Content;
use PHPUnit\Framework\TestCase;

class PreviousSiblingTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = Reader::of();
    }

    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $tree = ($this->read)(
            Content::ofString($xml),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $div = $tree
            ->children()
            ->get(0);
        $bar = $div
            ->map(static fn($div) => $div->children())
            ->flatMap(static fn($children) => $children->get(2))
            ->match(
                static fn($node) => $node,
                static fn() => null,
            );
        $baz = $div
            ->map(static fn($div) => $div->children())
            ->flatMap(static fn($children) => $children->get(1))
            ->match(
                static fn($node) => $node,
                static fn() => null,
            );

        $this->assertSame(
            $baz,
            PreviousSibling::of($bar)($tree)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
    }

    public function testReturnNothingWhenNoPreviousSibling()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $tree = ($this->read)(
            Content::ofString($xml),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );
        $div = $tree
            ->children()
            ->get(0);
        $foo = $div
            ->map(static fn($div) => $div->children())
            ->flatMap(static fn($children) => $children->get(0))
            ->match(
                static fn($foo) => $foo,
                static fn() => null,
            );

        $this->assertNull(PreviousSibling::of($foo)($tree)->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
