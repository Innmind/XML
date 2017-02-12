<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\FirstChild,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator,
    Translator\NodeTranslators
};
use Innmind\Filesystem\Stream\StringStream;
use PHPUnit\Framework\TestCase;

class FirstChildTest extends TestCase
{
    private $reader;

    public function setUp()
    {
        $this->reader = new Reader(
            new NodeTranslator(
                NodeTranslators::defaults()
            )
        );
    }

    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><bar /></div>
XML;
        $tree = $this->reader->read(
            new StringStream($xml)
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

    /**
     * @expectedException Innmind\Xml\Exception\NodeDoesntHaveChildrenException
     */
    public function testThrowWhenNoFirstChild()
    {
        (new FirstChild)(new Element('foo'));
    }
}
