<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\Reader,
    Reader as ReaderInterface,
    Element\Element,
    Translator\Translator,
    Translator\NodeTranslators,
};
use Innmind\Stream\Readable\Stream;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = new Reader(
            new Translator(
                NodeTranslators::defaults(),
            ),
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(
            ReaderInterface::class,
            $this->read,
        );
    }

    public function testUseDefaultTranslatorWhenNoneProvided()
    {
        $this->assertEquals(
            $this->read,
            new Reader,
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
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, $xml);
        $node = ($this->read)(Stream::of($res))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertSame($xml, $node->toString());
    }

    public function testReturnNothingWhenEmpty()
    {
        $res = \fopen('php://temp', 'r+');
        $node = ($this->read)(Stream::of($res))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }

    public function testReturnNothingWhenInvalidXml()
    {
        $res = \fopen('php://temp', 'r+');
        \fwrite($res, "<?xml version=\"1.0\"?>\n");
        $node = ($this->read)(Stream::of($res))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }
}
