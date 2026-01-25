<?php
declare(strict_types = 1);

namespace Innmind\Xml\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Type
{
    /**
     * @param non-empty-string $name
     */
    private function __construct(
        private string $name,
        private string $publicId,
        private string $systemId,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     *
     * @throws DomainException If the name is empty
     */
    #[\NoDiscard]
    public static function of(
        string $name,
        string $publicId = '',
        string $systemId = '',
    ): self {
        return self::maybe($name, $publicId, $systemId)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException,
        );
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    #[\NoDiscard]
    public static function maybe(
        string $name,
        string $publicId = '',
        string $systemId = '',
    ): Maybe {
        if ($name === '') {
            /** @var Maybe<self> */
            return Maybe::nothing();
        }

        return Maybe::just(new self($name, $publicId, $systemId));
    }

    /**
     * @return non-empty-string
     */
    #[\NoDiscard]
    public function name(): string
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function publicId(): string
    {
        return $this->publicId;
    }

    #[\NoDiscard]
    public function systemId(): string
    {
        return $this->systemId;
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return \sprintf(
            '<!DOCTYPE %s%s%s>',
            $this->name,
            $this->publicId ? ' PUBLIC "'.$this->publicId.'"' : '',
            $this->systemId ? ' "'.$this->systemId.'"' : '',
        );
    }
}
