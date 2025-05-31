<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Xml\{
    Element,
    Node,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Sequence,
    Str,
};

/**
 * @internal
 * @psalm-immutable
 */
final class Child
{
    private function __construct(
        private Element|Node|null $child,
        private bool $first,
        private bool $last,
    ) {
    }

    /**
     * @psalm-pure
     */
    public static function placeholder(): self
    {
        return new self(
            null,
            true,
            true,
        );
    }

    /**
     * @psalm-pure
     */
    public static function of(Element|Node $child): self
    {
        return new self(
            $child,
            true,
            true,
        );
    }

    /**
     * @return Sequence<self>
     */
    public function followedBy(self $next): Sequence
    {
        if (\is_null($this->child)) {
            return Sequence::of($next);
        }

        return Sequence::of(
            new self(
                $this->child,
                $this->first,
                false,
            ),
            new self(
                $next->child,
                false,
                $this->last,
            ),
        );
    }

    public function render(
        string $openingTag,
        string $closingTag,
    ): Content {
        if (\is_null($this->child)) {
            return Content::ofString($openingTag.$closingTag);
        }

        if ($this->first && $this->last && $this->child instanceof Node) {
            return Content::ofChunks(
                $this
                    ->child
                    ->asContent()
                    ->chunks()
                    ->prepend(Sequence::of(Str::of($openingTag)))
                    ->add(Str::of($closingTag)),
            );
        }

        $before = Sequence::of(Str::of("\n"));
        $after = Sequence::of();

        if ($this->first) {
            $before = Sequence::of(Str::of($openingTag."\n"));
        }

        if ($this->last) {
            $after = Sequence::of(Str::of("\n".$closingTag));
        }

        return Content::ofChunks(
            $this
                ->child
                ->asContent()
                ->lines()
                ->map(static fn($line) => $line->str())
                ->map(static fn($line) => $line->prepend('    ')) // to correctly indent the file
                ->prepend($before)
                ->append($after),
        );
    }
}
