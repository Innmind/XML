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
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function read(Readable $content): Node
    {
        $xml = new \DOMDocument;
        $xml->loadXML((string) $content);
        $xml->normalizeDocument();

        return $this->translator->translate($xml);
    }
}
