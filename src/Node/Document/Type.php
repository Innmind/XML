<?php
declare(strict_types = 1);

namespace Innmind\Xml\Node\Document;

use Innmind\Xml\Exception\DomainException;
use Innmind\Immutable\Str;

final class Type
{
    private string $name;
    private string $publicId;
    private string $systemId;
    private string $string;

    public function __construct(
        string $name,
        string $publicId = '',
        string $systemId = ''
    ) {
        if (Str::of($name)->empty()) {
            throw new DomainException;
        }

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
