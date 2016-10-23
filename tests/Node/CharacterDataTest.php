<?php
declare(strict_types = 1);

namespace Tests\Innmind\XML\Node;

use Innmind\XML\{
    Node\CharacterData,
    NodeInterface
};
use Innmind\Immutable\MapInterface;

class CharacterDataTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NodeInterface::class,
            new CharacterData('foo')
        );
    }

    public function testChildren()
    {
        $characterData = new CharacterData('foo');

        $this->assertInstanceOf(MapInterface::class, $characterData->children());
        $this->assertSame('int', (string) $characterData->children()->keyType());
        $this->assertSame(
            NodeInterface::class,
            (string) $characterData->children()->ValueType()
        );
        $this->assertCount(0, $characterData->children());
        $this->assertFalse($characterData->hasChildren());
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            (new CharacterData(' foo '))->content()
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<![CDATA[foo]]>',
            (string) new CharacterData('foo')
        );
    }
}
