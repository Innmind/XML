<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\FirstChild,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
    Exception\NodeDoesntHaveChildren,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class FirstChildTest extends TestCase
{
    private $read;

    public function setUp(): void
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
<div><foo /><bar /></div>
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

        $this->assertSame(
            $foo,
            (new FirstChild)($div)
        );
    }

    public function testThrowWhenNoFirstChild()
    {
        $this->expectException(NodeDoesntHaveChildren::class);

        (new FirstChild)(new Element('foo'));
    }
}
