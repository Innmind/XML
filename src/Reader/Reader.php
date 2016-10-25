<?php
declare(strict_types = 1);

namespace Innmind\XML\Reader;

use Innmind\XML\{
    ReaderInterface,
    NodeInterface,
    Translator\NodeTranslatorInterface
};
use Innmind\Filesystem\StreamInterface;

final class Reader implements ReaderInterface
{
    private $translator;

    public function __construct(NodeTranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function read(StreamInterface $content): NodeInterface
    {
        $xml = new \DOMDocument;
        $xml->loadXML((string) $content);
        $xml->normalizeDocument();

        return $this->translator->translate($xml);
    }
}
