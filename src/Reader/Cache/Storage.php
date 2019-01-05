<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader\Cache;

use Innmind\Xml\Node;
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;

final class Storage
{
    private $map;

    public function __construct()
    {
        $this->map = new Map(Readable::class, Node::class);
    }

    public function add(Readable $xml, Node $node): void
    {
        $this->map = $this->map->put($xml, $node);
    }

    public function contains(Readable $xml): bool
    {
        return $this->map->contains($xml);
    }

    public function get(Readable $xml): Node
    {
        return $this->map->get($xml);
    }

    public function remove(Readable $xml): void
    {
        $this->map = $this->map->remove($xml);
    }
}
