<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node;

use Innmind\Xml\Node;
use Innmind\Immutable\Sequence;

/**
 * @psalm-immutable
 * @see http://xmlplease.com/xml/pi/
 */
final class ProcessingInstruction implements Node
{
    private string $kind;
    private string $value;

    private function __construct(string $kind, string $value)
    {
        $this->kind = $kind;
        $this->value = $value;
    }

    /**
     * @psalm-pure
     */
    public static function of(string $kind, string $value): self
    {
        return new self($kind, $value);
    }

    public function children(): Sequence
    {
        return Sequence::of();
    }

    public function filterChild(callable $filter): self
    {
        return $this;
    }

    public function mapChild(callable $map): self
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function prependChild(Node $child): Node
    {
        return $this;
    }

    /**
     * This operation will do nothing
     */
    public function appendChild(Node $child): Node
    {
        return $this;
    }

    public function content(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return \sprintf(
            '<?%s %s?>',
            $this->kind,
            $this->value,
        );
    }
}
