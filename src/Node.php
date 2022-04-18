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
    public function removeChild(int $position): self;
    public function replaceChild(int $position, self $child): self;
    public function prependChild(self $child): self;
    public function appendChild(self $child): self;
    public function content(): string;
    public function toString(): string;
}
