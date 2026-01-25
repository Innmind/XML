<?php
declare(strict_types = 1);

namespace Innmind\Xml\Element;

use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Name
{
    /**
     * @param ?non-empty-string $namespace
     * @param non-empty-string $value
     */
    private function __construct(
        private ?string $namespace,
        private string $value,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $value
     */
    #[\NoDiscard]
    public static function of(string $value): self
    {
        return new self(null, $value);
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $namespace
     * @param non-empty-string $value
     */
    #[\NoDiscard]
    public static function namespaced(string $namespace, string $value): self
    {
        return new self($namespace, $value);
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    #[\NoDiscard]
    public static function maybe(string $value): Maybe
    {
        /** @var Maybe<self> */
        return match ($value) {
            '' => Maybe::nothing(),
            default => Maybe::just(new self(null, $value)),
        };
    }

    /**
     * @return non-empty-string
     */
    #[\NoDiscard]
    public function toString(): string
    {
        if ($this->namespace !== null) {
            return \sprintf(
                '%s:%s',
                $this->namespace,
                $this->value,
            );
        }

        return $this->value;
    }

    private function render(\XMLWriter $writer): void
    {
        if (\is_null($this->namespace)) {
            /** @psalm-suppress ImpureMethodCall */
            $writer->startElement($this->value);
        } else {
            /** @psalm-suppress ImpureMethodCall */
            $writer->startElementNS(
                $this->namespace,
                $this->value,
                null,
            );
        }
    }
}
