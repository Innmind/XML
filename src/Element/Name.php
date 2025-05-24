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
     * @param non-empty-string $value
     */
    private function __construct(
        private string $value,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $value
     */
    public static function of(string $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function maybe(string $value): Maybe
    {
        /** @var Maybe<self> */
        return match ($value) {
            '' => Maybe::nothing(),
            default => Maybe::just(new self($value)),
        };
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
