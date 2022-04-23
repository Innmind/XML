<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Visitor;

use Innmind\Xml\{
    Visitor\Text,
    Reader\Reader,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = Reader::of();
    }

    public function testInterface()
    {
        $xml = <<<XML
<div>
    <h1>Hey</h1>
    <div>
        <foo />
        whatever
        <bar />
    </div>
    42
</div>
XML;
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $tree = ($this->read)(
            Stream::of($res)
        )->match(
            static fn($node) => $node,
            static fn() => null,
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
            Text::of()($tree),
        );
    }
}
