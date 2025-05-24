<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\{
    Reader,
    Document,
};
use Innmind\Filesystem\{
    Adapter\Filesystem,
    File,
    File\Content,
    Name,
};
use Innmind\Url\Path;
use Innmind\Immutable\Predicate\Instance;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    private $read;

    public function setUp(): void
    {
        $this->read = Reader::of();
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
        $node = ($this->read)(Content::ofString($xml))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertSame($xml, $node->toString());
    }

    public function testReturnNothingWhenEmpty()
    {
        $node = ($this->read)(Content::none())->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }

    public function testReturnNothingWhenInvalidXml()
    {
        $node = ($this->read)(Content::ofString("<?xml version=\"1.0\"?>\n"))->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertNull($node);
    }

    public function testProcessingInstructionsAreReadCorrectly()
    {
        $content = Filesystem::mount(Path::of('fixtures/'))
            ->get(Name::of('theatlantic.xml'))
            ->keep(Instance::of(File::class))
            ->match(
                static fn($file) => $file->content(),
                static fn() => null,
            );

        $node = ($this->read)($content)->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Document::class, $node);
        $this->assertCount(2, $node->children());
        $stylesheet = $node->children()->first()->match(
            static fn($stylesheet) => $stylesheet,
            static fn() => null,
        );
        $this->assertSame(
            '<?xml-stylesheet type="text/xsl" href="/static/theatlantic/syndication/feeds/atom-to-html.6d0fbcbe7c3f.xsl" ?>',
            $stylesheet->toString(),
        );
    }
}
