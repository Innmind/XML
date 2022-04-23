<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Version
{
    /** @var 0|positive-int */
    private int $major;
    /** @var 0|positive-int */
    private int $minor;
    private string $string;

    /**
     * @param 0|positive-int $major
     * @param 0|positive-int $minor
     */
    private function __construct(int $major, int $minor)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->string = $major.'.'.$minor;
    }

    /**
     * @psalm-pure
     *
     * @param 0|positive-int $major
     * @param 0|positive-int $minor
     *
     * @throws DomainException
     */
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
    public static function maybe(int $major, int $minor = 0): Maybe
    {
        $major = Maybe::just($major)->filter(static fn($int) => $int >= 0);
        $minor = Maybe::just($minor)->filter(static fn($int) => $int >= 0);

        /** @psalm-suppress ArgumentTypeCoercion */
        return Maybe::all($major, $minor)
            ->map(static fn(int $major, int $minor) => new self($major, $minor));
    }

    /**
     * @return 0|positive-int
     */
    public function major(): int
    {
        return $this->major;
    }

    /**
     * @return 0|positive-int
     */
    public function minor(): int
    {
        return $this->minor;
    }

    public function toString(): string
    {
        return $this->string;
    }
}
