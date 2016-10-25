<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\FirstChild,
    Reader\Reader,
    Element\Element
};

class FirstChildTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $tree = (new Reader)->read(<<<XML
<div><foo /><bar /></div>
XML
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
     * @expectedException Innmind\XML\Exception\NodeDoesntHaveChildrenException
     */
    public function testThrowWhenNoFirstChild()
    {
        (new FirstChild)(new Element('foo'));
    }
}
