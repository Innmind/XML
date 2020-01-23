<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader as ReaderInterface,
    Node,
    Translator\Translator,
};
use Innmind\Stream\Readable;

final class Reader implements ReaderInterface
{
    private Translator $translate;

    public function __construct(Translator $translate)
    {
        $this->translate = $translate;
    }

    public function __invoke(Readable $content): Node
    {
        $xml = new \DOMDocument;
        $xml->loadXML((string) $content);
        $xml->normalizeDocument();

        return ($this->translate)($xml);
    }
}
