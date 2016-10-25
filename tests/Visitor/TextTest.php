<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Visitor;

use Innmind\XML\{
    Visitor\Text,
    Reader\Reader,
    Translator\NodeTranslator
};

class TextTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $tree = (new Reader(new NodeTranslator))->read(<<<XML
<div>
    <h1>Hey</h1>
    <div>
        <foo />
        whatever
        <bar />
    </div>
    42
</div>
XML
        );

        $this->assertSame(
            "\n".
            '    Hey'."\n".
            '    '."\n".
            '        '."\n".
            '        whatever'."\n".
            '        '."\n".
            '    '."\n".
            '    42'."\n",
            (new Text)($tree)
        );
    }
}
