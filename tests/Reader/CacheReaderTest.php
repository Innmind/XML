<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\CacheReader,
    ReaderInterface,
    NodeInterface
};
use Innmind\Filesystem\StreamInterface;
use PHPUnit\Framework\TestCase;

class CacheReaderTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ReaderInterface::class,
            new CacheReader(
                $this->createMock(ReaderInterface::class)
            )
        );
    }

    public function testCache()
    {
        $stream = $this->createMock(StreamInterface::class);
        $cache = new CacheReader(
            $reader = $this->createMock(ReaderInterface::class)
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
        $this->assertSame($cache, $cache->detach($stream));
        $this->assertNotSame($node, $cache->read($stream));
    }
}
