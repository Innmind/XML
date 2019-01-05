<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\Cache,
    Reader\Cache\Storage,
    ReaderInterface,
    NodeInterface,
};
use Innmind\Stream\Readable;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ReaderInterface::class,
            new Cache(
                $this->createMock(ReaderInterface::class),
                new Storage
            )
        );
    }

    public function testCache()
    {
        $stream = $this->createMock(Readable::class);
        $cache = new Cache(
            $reader = $this->createMock(ReaderInterface::class),
            $storage = new Storage
        );
        $reader
            ->method('read')
            ->with($stream)
            ->willReturnCallback(function($xml) {
                return $this->createMock(NodeInterface::class);
            });

        $node = $cache->read($stream);
        $this->assertInstanceOf(NodeInterface::class, $node);
        $this->assertSame($node, $cache->read($stream));
        $storage->remove($stream);
        $this->assertNotSame($node, $cache->read($stream));
    }
}
