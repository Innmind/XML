<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator,
    Node,
};
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class EntityReferenceTranslatorTest extends TestCase
{
    public function testTranslate()
    {
        $translate = Translator::of();
        $node = $translate(
            new \DOMEntityReference('gt'),
        )->match(
            static fn($node) => $node,
            static fn() => null,
        );

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('gt', $node->content());
    }
}
