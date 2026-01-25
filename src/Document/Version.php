<?php
declare(strict_types = 1);

namespace Innmind\Xml\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Version
{
    /**
     * @param int<0, max> $major
     * @param int<0, max> $minor
     */
    private function __construct(
        private int $major,
        private int $minor,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param int<0, max> $major
     * @param int<0, max> $minor
     *
     * @throws DomainException
     */
    #[\NoDiscard]
    public static function of(int $major, int $minor = 0): self
    {
        return self::maybe($major, $minor)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException("$major.$minor"),
        );
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    #[\NoDiscard]
    public static function maybe(int $major, int $minor = 0): Maybe
    {
        $major = Maybe::just($major)->filter(static fn($int) => $int >= 0);
        $minor = Maybe::just($minor)->filter(static fn($int) => $int >= 0);

        /** @psalm-suppress ArgumentTypeCoercion */
        return Maybe::all($major, $minor)
            ->map(static fn(int $major, int $minor) => new self($major, $minor));
    }

    /**
     * @return int<0, max>
     */
    #[\NoDiscard]
    public function major(): int
    {
        return $this->major;
    }

    /**
     * @return int<0, max>
     */
    #[\NoDiscard]
    public function minor(): int
    {
        return $this->minor;
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return $this->major.'.'.$this->minor;
    }
}
