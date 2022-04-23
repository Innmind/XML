<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\Reader,
    Reader as ReaderInterface,
    Element\Element,
};
use Innmind\Filesystem\File\Content;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = Reader::of();
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
            Reader::of(),
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
        $node = ($this->read)(Content\Lines::ofContent($xml))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertSame($xml, $node->toString());
    }

    public function testReturnNothingWhenEmpty()
    {
        $node = ($this->read)(Content\None::of())->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }

    public function testReturnNothingWhenInvalidXml()
    {
        $node = ($this->read)(Content\Lines::ofContent("<?xml version=\"1.0\"?>\n"))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }
}
