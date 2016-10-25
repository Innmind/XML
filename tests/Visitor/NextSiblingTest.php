<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\NextSibling,
    Reader\Reader,
    Element\Element
};

class NextSiblingTest extends \PHPUnit_Framework_TestCase
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
            $bar,
            (new NextSibling($baz))($tree)
        );
    }

    /**
     * @expectedException Innmind\XML\Exception\NoNextSiblingException
     */
    public function testThrowWhenNoNextSibling()
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

        (new NextSibling($bar))($tree);
    }
}
