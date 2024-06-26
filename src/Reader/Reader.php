<?php
declare(strict_types = 1);

namespace Innmind\Xml\Reader;

use Innmind\Xml\{
    Reader as ReaderInterface,
    Translator\Translator,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Reader implements ReaderInterface
{
    private Translator $translate;

    private function __construct(Translator $translate = null)
    {
        $this->translate = $translate ?? Translator::default();
    }

    public function __invoke(Content $content): Maybe
    {
        return Maybe::just($content->toString())
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

    public static function of(Translator $translate = null): self
    {
        return new self($translate);
    }
}
