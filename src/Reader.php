<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Xml\Element\Custom;
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\Attempt;

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
     * @return Attempt<Document|Node|Element|Custom>
     */
    public function __invoke(Content $content): Attempt
    {
        $content = $content->toString();

        if ($content === '') {
            return Attempt::error(new \RuntimeException('Empty content'));
        }

        $xml = new \DOMDocument;
        /** @psalm-suppress ImpureMethodCall */
        $success = $xml->loadXML(
            $content,
            \LIBXML_ERR_ERROR | \LIBXML_NOWARNING | \LIBXML_NOERROR,
        );

        if (!$success) {
            return Attempt::error(new \RuntimeException('Failed to load xml content'));
        }

        /** @psalm-suppress ImpureMethodCall */
        $xml->normalizeDocument();

        return ($this->translate)($xml);
    }

    public static function of(): self
    {
        return new self();
    }
}
