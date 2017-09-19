<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    ReaderInterface,
    NodeInterface,
    Translator\NodeTranslator
};
use Innmind\Stream\Readable;

final class Reader implements ReaderInterface
{
    private $translator;

    public function __construct(NodeTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function read(Readable $content): NodeInterface
    {
        $xml = new \DOMDocument;
        $xml->loadXML((string) $content);
        $xml->normalizeDocument();

        return $this->translator->translate($xml);
    }
}
