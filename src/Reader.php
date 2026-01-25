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

        try {
            $xml = \Dom\XMLDocument::createFromString(
                $content,
                \LIBXML_ERR_ERROR | \LIBXML_NOWARNING | \LIBXML_NOERROR,
            );
        } catch (\Throwable $e) {
            return Attempt::error($e);
        }

        return ($this->translate)($xml);
    }

    public static function of(): self
    {
        return new self();
    }
}
