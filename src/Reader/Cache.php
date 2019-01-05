<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader,
    Reader\Cache\Storage,
    Node,
};
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;

final class Cache implements Reader
{
    private $reader;
    private $cache;

    public function __construct(Reader $reader, Storage $cache)
    {
        $this->reader = $reader;
        $this->cache = $cache;
    }

    public function read(Readable $xml): Node
    {
        if ($this->cache->contains($xml)) {
            return $this->cache->get($xml);
        }

        $node = $this->reader->read($xml);
        $this->cache->add($xml, $node);

        return $node;
    }
}
