<?php
declare(strict_types = 1);

namespace Innmind\XML\Node\Document;

use Innmind\XML\Exception\InvalidArgumentException;

final class Type
{
    private $name;
    private $publicId;
    private $systemId;
    private $string;

    public function __construct(
        string $name,
        string $publicId = '',
        string $systemId = ''
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException;
        }

        $this->name = $name;
        $this->publicId = $publicId;
        $this->systemId = $systemId;
        $this->string = sprintf(
            '<!DOCTYPE %s%s%s>',
            $name,
            $publicId ? ' PUBLIC "'.$publicId.'"' : '',
            $systemId ? ' "'.$systemId.'"' : ''
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

    public function __toString(): string
    {
        return $this->string;
    }
}
