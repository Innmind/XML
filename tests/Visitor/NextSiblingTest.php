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
    private $reader;

    public function setUp()
    {
        $this->reader = new Reader(
            new Translator(
                NodeTranslators::defaults()
            )
        );
    }

    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $res = fopen('php://temp', 'r+');
        fwrite($res, $xml);
        $tree = $this->reader->read(
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
            (new NextSibling($baz))($tree)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\NoNextSibling
     */
    public function testThrowWhenNoNextSibling()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $res = fopen('php://temp', 'r+');
        fwrite($res, $xml);
        $tree = $this->reader->read(
            new Stream($res)
        );
        $div = $tree
            ->children()
            ->get(0);
        $bar = $div
            ->children()
            ->get(2);

        (new NextSibling($bar))($tree);
    }
}
