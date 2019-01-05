<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader\Cache;

use Innmind\Xml\{
    Reader\Cache\Storage,
    Node,
};
use Innmind\Stream\Readable;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    public function testInterface()
    {
        $storage = new Storage;

        $xml = $this->createMock(Readable::class);
        $node = $this->createMock(Node::class);

        $this->assertFalse($storage->contains($xml));
        $this->assertNull($storage->add($xml, $node));
        $this->assertTrue($storage->contains($xml));
        $this->assertNull($storage->remove($xml));
        $this->assertFalse($storage->contains($xml));
    }
}
