<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\ParentNode,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator
};

class ParentNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $tree = (new Reader(new NodeTranslator))->read(<<<XML
<div><div><foo /><bar /></div></div>
XML
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
     * @expectedException Innmind\XML\Exception\NodeHasNoParentException
     */
    public function testThrowWhenNoParentFound()
    {
        (new ParentNode(new Element('foo')))(new Element('bar'));
    }
}
