<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    ReaderInterface,
    NodeInterface
};
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;

final class CacheReader implements ReaderInterface
{
    private $reader;
    private $cache;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->cache = new Map(Readable::class, NodeInterface::class);
    }

    public function read(Readable $xml): NodeInterface
    {
        if ($this->cache->contains($xml)) {
            return $this->cache->get($xml);
        }

        $node = $this->reader->read($xml);
        $this->cache = $this->cache->put($xml, $node);

        return $node;
    }

    public function detach(Readable $xml): self
    {
        $this->cache = $this->cache->remove($xml);

        return $this;
    }
}
