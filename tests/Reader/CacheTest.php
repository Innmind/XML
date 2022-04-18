<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader\Cache,
    Reader\Cache\Storage,
    Reader,
    Node,
};
use Innmind\Stream\Readable;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Reader::class,
            new Cache(
                $this->createMock(Reader::class),
                new Storage,
            ),
        );
    }

    public function testCache()
    {
        $stream = $this->createMock(Readable::class);
        $read = new Cache(
            $reader = $this->createMock(Reader::class),
            $storage = new Storage,
        );
        $reader
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->with($stream)
            ->willReturnCallback(function($xml) {
                return $this->createMock(Node::class);
            });

        $node = $read($stream);
        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame($node, $read($stream));
        $storage->remove($stream);
        $this->assertNotSame($node, $read($stream));
    }
}
