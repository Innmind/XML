<?php
declare(strict_types = 1);

namespace Innmind\XML\Reader;

use Innmind\XML\{
    ReaderInterface,
    NodeInterface,
    Translator\NodeTranslatorInterface
};

final class Reader implements ReaderInterface
{
    private $translator;

    public function __construct(NodeTranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function read(string $content): NodeInterface
    {
        $xml = new \DOMDocument;
        $xml->loadXML($content);
        $xml->normalizeDocument();

        return $this->translator->translate($xml);
    }
}
