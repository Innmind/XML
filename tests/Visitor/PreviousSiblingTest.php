<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\PreviousSibling,
    Reader\Reader,
    Element\Element
};

class PreviousSiblingTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $tree = (new Reader)->read(<<<XML
<div><foo /><baz /><bar /></div>
XML
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
            $baz,
            (new PreviousSibling($bar))($tree)
        );
    }

    /**
     * @expectedException Innmind\XML\Exception\NoPreviousSiblingException
     */
    public function testThrowWhenNoPreviousSibling()
    {
        $tree = (new Reader)->read(<<<XML
<div><foo /><baz /><bar /></div>
XML
        );
        $div = $tree
            ->children()
            ->get(0);
        $foo = $div
            ->children()
            ->get(0);

        (new PreviousSibling($foo))($tree);
    }
}
