<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Reader;

use Innmind\XML\{
    Reader\Reader,
    ReaderInterface,
    Element\Element,
    Translator\NodeTranslatorInterface
};
use Innmind\Filesystem\Stream\StringStream;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ReaderInterface::class,
            new Reader(
                $this->createMock(NodeTranslatorInterface::class)
            )
        );
    }

    public function testRead()
    {
        $reader = new Reader(
            $translator = $this->createMock(NodeTranslatorInterface::class)
        );
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
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with($this->callback(function(\DOMDocument $document) use ($xml) {
                return $document->saveXML() === $xml."\n";
            }))
            ->willReturn($expected = new Element('foo'));
        $node = $reader->read(new StringStream($xml));

        $this->assertSame($expected, $node);
    }
}
