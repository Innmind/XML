<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\ParentNode,
    Reader\Reader,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class ParentNodeTest extends TestCase
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
<div><div><foo /><bar /></div></div>
XML;
        $res = fopen('php://temp', 'r+');
        fwrite($res, $xml);
        $tree = $this->reader->read(
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

    /**
     * @expectedException Innmind\Xml\Exception\NodeHasNoParentException
     */
    public function testThrowWhenNoParentFound()
    {
        (new ParentNode(new Element('foo')))(new Element('bar'));
    }
}
