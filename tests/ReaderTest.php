<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\{
    Reader,
    Document,
    Element,
    Node,
    Attribute,
};
use Innmind\Filesystem\{
    Adapter\Filesystem,
    File,
    File\Content,
    Name,
};
use Innmind\Url\Path;
use Innmind\Immutable;
use Innmind\Immutable\Predicate\Instance;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    Set,
};

class ReaderTest extends TestCase
{
    use BlackBox;

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
<?xml version="1.0" encoding="UTF-8"?>
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

    public function testAnyXmlTreeIsParseable(): BlackBox\Proof
    {
        $names = Set::strings()
            ->madeOf(Set::strings()->chars()->lowercaseLetter())
            ->atLeast(1);
        $node = Set::either(
            Set::strings()
                ->madeOf(Set::strings()->chars()->ascii())
                ->map(Node::characterData(...)),
            Set::strings()
                ->madeOf(
                    Set::strings()
                        ->chars()
                        ->ascii()
                        ->filter(static fn($char) => !\in_array(
                            $char,
                            ['&', '<'],
                            true,
                        )),
                )
                ->map(Node::text(...)),
            Set::strings()
                ->madeOf(
                    Set::strings()
                        ->chars()
                        ->ascii()
                        ->filter(static fn($char) => !\in_array(
                            $char,
                            ['-'],
                            true,
                        )),
                )
                ->map(Node::comment(...)),
            Set::of('gt', 'lt', 'amp', 'apos', 'quot')->map(Node::entityReference(...)),
            Set::of(Node::processingInstruction(
                'xml-stylesheet',
                'type="text/xsl" href="/static/theatlantic/syndication/feeds/atom-to-html.6d0fbcbe7c3f.xsl"',
            )),
        );
        $attributes = Set::sequence(
            Set::either(
                $names
                    ->map(Attribute::of(...)),
                Set::compose(
                    Attribute::of(...),
                    $names,
                    $names,
                ),
            ),
        )
            ->between(0, 4)
            ->map(static fn($attributes) => Immutable\Set::of(...$attributes));
        $leaf = Set::either(
            Set::compose(
                Element::selfClosing(...),
                $names->map(Element\Name::of(...)),
                $attributes,
            ),
            Set::compose(
                Element::of(...),
                $names->map(Element\Name::of(...)),
                $attributes,
            ),
        );
        $children = static function(int $recurse = 1) use (&$children, $leaf, $node, $names, $attributes) {
            if ($recurse !== 1) {
                return Set::of(Immutable\Sequence::of());
            }

            return Set::sequence(
                Set::either(
                    Set::integers()
                        ->between(0, 25) // 4% chance to recurse
                        ->flatMap(static fn($recurse) => $children(
                            $recurse->unwrap(),
                        ))
                        ->flatMap(static fn($children) => Set::compose(
                            static fn($name, $attributes) => $children->map(
                                static fn($children) => Element::of(
                                    $name,
                                    $attributes,
                                    $children,
                                ),
                            ),
                            $names->map(Element\Name::of(...)),
                            $attributes,
                        )),
                    $leaf,
                    $node,
                ),
            )->map(static fn($values) => Immutable\Sequence::of(...$values));
        };
        $element = Set::compose(
            Element::of(...),
            $names->map(Element\Name::of(...)),
            $attributes,
            $children(),
        );
        $document = Set::compose(
            Document::of(...),
            Set::of(
                Document\Version::of(1, 0),
                Document\Version::of(1, 1),
            ),
            $names
                ->map(Document\Type::of(...))
                ->nullable()
                ->map(Immutable\Maybe::of(...)),
            Set::of(...Document\Encoding::cases())
                ->nullable()
                ->map(Immutable\Maybe::of(...)),
            Set::sequence($element)->between(1, 1)->map(
                static fn($elements) => Immutable\Sequence::of(...$elements),
            ),
        );

        return $this
            ->forAll(Set::either(
                $document,
                $element,
                // a node needs to be in a document
                $node->map(
                    static fn($node) => Document::of(
                        Document\Version::of(1, 0),
                        Immutable\Maybe::nothing(),
                        Immutable\Maybe::nothing(),
                    )
                        ->appendChild(Element::of(
                            Element\Name::of('root'),
                            null,
                            Immutable\Sequence::of($node),
                        )),
                ),
                $leaf,
            ))
            ->prove(function($tree) {
                $this->assertNotNull(
                    ($this->read)($tree->asContent())->match(
                        static fn($node) => $node,
                        static fn() => null,
                    ),
                    $tree->asContent()->toString(),
                );
            });
    }
}
