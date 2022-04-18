<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader as ReaderInterface,
    Node,
    Translator\Translator,
};
use Innmind\Stream\Readable;
use Innmind\Immutable\Maybe;

final class Reader implements ReaderInterface
{
    private Translator $translate;

    public function __construct(Translator $translate = null)
    {
        $this->translate = $translate ?? Translator::default();
    }

    public function __invoke(Readable $content): Maybe
    {
        return $content
            ->toString()
            ->filter(static fn($content) => $content !== '')
            ->flatMap(static function($content): Maybe {
                $xml = new \DOMDocument;
                /** @psalm-suppress ArgumentTypeCoercion */
                $success = $xml->loadXML(
                    $content,
                    \LIBXML_ERR_ERROR | \LIBXML_NOWARNING | \LIBXML_NOERROR,
                );

                if (!$success) {
                    /** @var Maybe<\DOMDocument> */
                    return Maybe::nothing();
                }

                $xml->normalizeDocument();

                return Maybe::just($xml);
            })
            ->flatMap($this->translate);
    }
}
