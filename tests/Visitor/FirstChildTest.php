<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\FirstChild,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator
};
use Innmind\Filesystem\Stream\StringStream;

class FirstChildTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $xml = <<<XML
<div><foo /><bar /></div>
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
