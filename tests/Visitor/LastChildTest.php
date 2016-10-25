<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\LastChild,
    Reader\Reader,
    Element\Element,
    Translator\NodeTranslator
};

class LastChildTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $tree = (new Reader(new NodeTranslator))->read(<<<XML
<div><foo /><baz /><bar /></div>
XML
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

        $tree = (new Reader(new NodeTranslator))->read(<<<XML
<div><foo /></div>
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
            (new LastChild)($div)
        );
    }

    /**
     * @expectedException Innmind\XML\Exception\NodeDoesntHaveChildrenException
     */
    public function testThrowWhenNoLastChild()
    {
        (new LastChild)(new Element('foo'));
    }
}
