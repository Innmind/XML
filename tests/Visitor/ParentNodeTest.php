<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\ParentNode,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
    Exception\NodeHasNoParent,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class ParentNodeTest extends TestCase
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
<div><div><foo /><bar /></div></div>
XML;
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            new Stream($res)
        );
        $parent = $tree
            ->children()
            ->get(0)
            ->children()
            ->get(0);
        $node = $parent
            ->children()
            ->get(1);

        $this->assertSame(
            $parent,
            (new ParentNode($node))($tree)
        );
    }

    public function testThrowWhenNoParentFound()
    {
        $this->expectException(NodeHasNoParent::class);

        (new ParentNode(new Element('foo')))(new Element('bar'));
    }
}
