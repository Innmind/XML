<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\{
    Translator\Translator,
    Translator\NodeTranslators,
};

/**
 * @return array{reader: Reader, cache_storage: Reader\Cache\Storage, cache: callable(Reader): Reader}
 */
function bootstrap(): array {
    return [
        'reader' => new Reader\Reader(
            new Translator(
                NodeTranslators::defaults(),
            ),
        ),
        'cache_storage' => $storage = new Reader\Cache\Storage,
        'cache' => static function(Reader $reader) use ($storage): Reader {
            return new Reader\Cache($reader, $storage);
        },
    ];
}
