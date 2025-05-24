<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 */
interface Node
{
    /**
     * @return Sequence<self|Element>
     */
    public function children(): Sequence;

    /**
     * @param callable(self|Element): bool $filter
     */
    public function filterChild(callable $filter): self;

    /**
     * @param callable(self|Element): (self|Element) $map
     */
    public function mapChild(callable $map): self;
    public function prependChild(self|Element $child): self;
    public function appendChild(self|Element $child): self;
    public function content(): string;
    public function toString(): string;
}
