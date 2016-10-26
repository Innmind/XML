<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\LastChild,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator
};
use Innmind\Filesystem\Stream\StringStream;

class LastChildTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><baz /><bar /></div>
XML;
        $tree = (new Reader(new NodeTranslator))->read(
            new StringStream($xml)
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
        $tree = (new Reader(new NodeTranslator))->read(
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
            (new LastChild)($div)
        );
    }

    /**
     * @expectedException Innmind\Xml\Exception\NodeDoesntHaveChildrenException
     */
    public function testThrowWhenNoLastChild()
    {
        (new LastChild)(new Element('foo'));
    }
}
