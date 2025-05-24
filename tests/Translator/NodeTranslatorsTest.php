<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Translator;

use Innmind\Xml\Translator\{
    NodeTranslators,
    NodeTranslator\ElementTranslator,
};
use Innmind\Immutable\Map;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class NodeTranslatorsTest extends TestCase
{
    public function testDefaults()
    {
        $defaults = NodeTranslators::defaults();

        $this->assertInstanceOf(Map::class, $defaults);
        $this->assertCount(1, $defaults);
        $this->assertInstanceOf(
            ElementTranslator::class,
            $defaults->get(\XML_ELEMENT_NODE)->match(
                static fn($translator) => $translator,
                static fn() => null,
            ),
        );
    }
}
