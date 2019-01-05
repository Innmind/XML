<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    ReaderInterface,
    Reader\Cache\Storage,
    NodeInterface,
};
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;

final class Cache implements ReaderInterface
{
    private $reader;
    private $cache;

    public function __construct(ReaderInterface $reader, Storage $cache)
    {
        $this->reader = $reader;
        $this->cache = $cache;
    }

    public function read(Readable $xml): NodeInterface
    {
        if ($this->cache->contains($xml)) {
            return $this->cache->get($xml);
        }

        $node = $this->reader->read($xml);
        $this->cache->add($xml, $node);

        return $node;
    }
}
