<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\PreviousSibling,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
    Exception\NoPreviousSibling,
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
            new Stream($res)
        );
        $div = $tree
            ->children()
            ->get(0);
        $bar = $div
            ->children()
            ->get(2);
        $baz = $div
            ->children()
            ->get(1);

        $this->assertSame(
            $baz,
            (new PreviousSibling($bar))($tree),
        );
    }

    public function testThrowWhenNoPreviousSibling()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            new Stream($res)
        );
        $div = $tree
            ->children()
            ->get(0);
        $foo = $div
            ->children()
            ->get(0);

        $this->expectException(NoPreviousSibling::class);

        (new PreviousSibling($foo))($tree);
    }
}
