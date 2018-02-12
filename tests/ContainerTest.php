<?php
declare(strict_types = 1);

namespace Tests\Innmind\Xml;

use Innmind\Xml\Reader\{
    Reader,
    CacheReader
};
use Innmind\Compose\{
    ContainerBuilder\ContainerBuilder,
    Loader\Yaml
};
use Innmind\Url\Path;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testServices()
    {
        $container = (new ContainerBuilder(new Yaml))(
            new Path('container.yml'),
            new Map('string', 'mixed')
        );

        $this->assertInstanceOf(Reader::class, $container->get('reader'));
        $this->assertInstanceOf(CacheReader::class, $container->get('cacheReader'));
    }
}
