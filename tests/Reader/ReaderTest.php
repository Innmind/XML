<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\Reader,
    ReaderInterface,
    Element\Element,
    Translator\NodeTranslator,
    Translator\NodeTranslators
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    private $reader;

    public function setUp()
    {
        $this->reader = new Reader(
            new NodeTranslator(
                NodeTranslators::defaults()
            )
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(
            ReaderInterface::class,
            $this->reader
        );
    }

    public function testRead()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<foo bar="baz">
    <foobar/>
    <div>
        <![CDATA[whatever]]>
    </div>
    <!--foobaz-->
    hey!
</foo>
XML;
        $res = fopen('php://temp', 'r+');
        fwrite($res, $xml);
        $node = $this->reader->read(new Stream($res));

        $this->assertSame($xml, (string) $node);
    }
}
