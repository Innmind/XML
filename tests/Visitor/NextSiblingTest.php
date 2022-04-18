<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\NextSibling,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
    Exception\NoNextSibling,
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
            $bar,
            (new NextSibling($baz))($tree),
        );
    }

    public function testThrowWhenNoNextSibling()
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

        $this->expectException(NoNextSibling::class);

        (new NextSibling($bar))($tree);
    }
}
