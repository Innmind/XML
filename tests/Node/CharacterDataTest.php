<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class CharacterDataTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Node::class,
            Node::characterData('foo'),
        );
    }

    public function testContent()
    {
        $this->assertSame(
            ' foo ',
            Node::characterData(' foo ')->content(),
        );
    }

    public function testCast()
    {
        $this->assertSame(
            '<![CDATA[foo]]>',
            Node::characterData('foo')->asContent()->toString(),
        );
    }
}
