<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\{
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Encoding
{
    private string $string;

    private function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @psalm-pure
     *
     * @throws DomainException
     */
    public static function of(string $string): self
    {
        return self::maybe($string)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException($string),
        );
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function maybe(string $string): Maybe
    {
        return Maybe::just(Str::of($string))
            ->filter(static fn($string) => $string->matches('~^[a-zA-Z0-9\-_:\(\)]+$~'))
            ->map(static fn($string) => new self($string->toString()));
    }

    public function toString(): string
    {
        return $this->string;
    }
}
