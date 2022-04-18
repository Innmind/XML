<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\PreviousSibling,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class PreviousSiblingTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = new Reader(
            new Translator(
                NodeTranslators::defaults(),
            ),
        );
    }

    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            Stream::of($res)
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
            (new PreviousSibling($bar))($tree)->match(
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
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            Stream::of($res)
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

        $this->assertNull((new PreviousSibling($foo))($tree)->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
