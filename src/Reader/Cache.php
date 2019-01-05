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
    private $read;
    private $cache;

    public function __construct(Reader $read, Storage $cache)
    {
        $this->read = $read;
        $this->cache = $cache;
    }

    public function __invoke(Readable $xml): Node
    {
        if ($this->cache->contains($xml)) {
            return $this->cache->get($xml);
        }

        $node = ($this->read)($xml);
        $this->cache->add($xml, $node);

        return $node;
    }
}
