<?php
declare(strict_types = 1);

namespace Innmind\XML\Node\Document;

use Innmind\XML\Exception\InvalidArgumentException;

final class Version
{
    private $major;
    private $minor;
    private $string;

    public function __construct(int $major, int $minor = 0)
    {
        if ($major < 0 || $minor < 0) {
            throw new InvalidArgumentException;
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

    public function __toString(): string
    {
        return $this->string;
    }
}
