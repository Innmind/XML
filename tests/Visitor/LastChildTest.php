<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\LastChild,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
    Exception\NodeDoesntHaveChildren,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class LastChildTest extends TestCase
{
    private $read;

    public function setUp()
    {
        $this->read = new Reader(
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
        $tree = ($this->read)(
            new Stream($res)
        );
        $div = $tree
            ->children()
            ->get(0);
        $bar = $div
            ->children()
            ->get(2);

        $this->assertSame(
            $bar,
            (new LastChild)($div)
        );

        $xml = <<<XML
<div><foo /></div>
XML;
        $res = fopen('php://temp', 'r+');
        fwrite($res, $xml);
        $tree = ($this->read)(
            new Stream($res)
        );
        $div = $tree
            ->children()
            ->get(0);
        $foo = $div
            ->children()
            ->get(0);

        $this->assertSame(
            $foo,
            (new LastChild)($div)
        );
    }

    public function testThrowWhenNoLastChild()
    {
        $this->expectException(NodeDoesntHaveChildren::class);

        (new LastChild)(new Element('foo'));
    }
}
