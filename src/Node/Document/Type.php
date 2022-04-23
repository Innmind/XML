<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Type
{
    /** @var non-empty-string */
    private string $name;
    private string $publicId;
    private string $systemId;
    private string $string;

    /**
     * @param non-empty-string $name
     */
    private function __construct(
        string $name,
        string $publicId,
        string $systemId,
    ) {
        $this->name = $name;
        $this->publicId = $publicId;
        $this->systemId = $systemId;
        $this->string = \sprintf(
            '<!DOCTYPE %s%s%s>',
            $name,
            $publicId ? ' PUBLIC "'.$publicId.'"' : '',
            $systemId ? ' "'.$systemId.'"' : '',
        );
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     *
     * @throws DomainException If the name is empty
     */
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
    public function name(): string
    {
        return $this->name;
    }

    public function publicId(): string
    {
        return $this->publicId;
    }

    public function systemId(): string
    {
        return $this->systemId;
    }

    public function toString(): string
    {
        return $this->string;
    }
}
