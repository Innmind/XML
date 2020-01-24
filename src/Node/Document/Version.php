<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;

final class Version
{
    private int $major;
    private int $minor;
    private string $string;

    public function __construct(int $major, int $minor = 0)
    {
        if ($major < 0 || $minor < 0) {
            throw new DomainException("$major.$minor");
        }

        $this->major = $major;
        $this->minor = $minor;
        $this->string = $major.'.'.$minor;
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
