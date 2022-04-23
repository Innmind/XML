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
     * @return Sequence<Node>
     */
    public function children(): Sequence;

    /**
     * @param callable(Node): bool $filter
     */
    public function filterChild(callable $filter): self;

    /**
     * @param callable(Node): Node $map
     */
    public function mapChild(callable $map): self;
    public function prependChild(self $child): self;
    public function appendChild(self $child): self;
    public function content(): string;
    public function toString(): string;
}
