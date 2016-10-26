<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\ParentNode,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator
};
use Innmind\Filesystem\Stream\StringStream;

class ParentNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $xml = <<<XML
<div><div><foo /><bar /></div></div>
XML;
        $tree = (new Reader(new NodeTranslator))->read(
            new StringStream($xml)
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
