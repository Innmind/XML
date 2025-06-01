<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Element\Custom;
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Reader
{
    private Translator $translate;

    private function __construct()
    {
        $this->translate = Translator::of();
    }

    /**
     * @return Maybe<Document|Node|Element|Custom>
     */
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

    public static function of(): self
    {
        return new self();
    }
}
