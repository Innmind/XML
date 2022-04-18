<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\NextSibling,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class NextSiblingTest extends TestCase
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
            $bar,
            (new NextSibling($baz))($tree)->match(
                static fn($node) => $node,
                static fn() => null,
            ),
        );
    }

    public function testReturnNothingWhenNoNextSibling()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            Stream::of($res)
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

        $this->assertNull((new NextSibling($bar))($tree)->match(
            static fn($node) => $node,
            static fn() => null,
        ));
    }
}
