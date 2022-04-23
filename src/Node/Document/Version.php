<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;

/**
 * @psalm-immutable
 */
final class Version
{
    private int $major;
    private int $minor;
    private string $string;

    private function __construct(int $major, int $minor)
    {
        if ($major < 0 || $minor < 0) {
            throw new DomainException("$major.$minor");
        }

        $this->major = $major;
        $this->minor = $minor;
        $this->string = $major.'.'.$minor;
    }

    /**
     * @psalm-pure
     */
    public static function of(int $major, int $minor = 0): self
    {
        return new self($major, $minor);
    }

    public function major(): int
    {
        return $this->major;
    }

    public function minor(): int
    {
        return $this->minor;
    }

    public function toString(): string
    {
        return $this->string;
    }
}
